<?php

/**
 * @todo Refractor internal names.  There are way too many methods with 
 *    "smart_search" or "SmartSearch" as part of their names.  They need to 
 *    define what they are doing instead of what they are being used by.
 */
class C_PatientFinder extends Controller {

	var $template_mod;
	var $_db;
	var $join_db;
	var $limit = 50;
	var $showNonPatients = false;
	
	function C_PatientFinder($template_mod = "general") {
		parent::Controller();
		$this->_db = $GLOBALS['frame']['adodb']['db']; 
		//$this->_join_db = $GLOBALS['frame']['config']['openemr_db'];
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", Celini::link(true,true,false) . $_SERVER['QUERY_STRING']);
		$this->assign("CURRENT_ACTION", Celini::link('patient_finder'));

		$this->view->path = 'patient_finder';
		
		//remove the trail entries for this because it is a popup
		//$trail = $_SESSION['trail'];
		//if(is_array($trail)) array_shift($trail);
		//$_SESSION['trail'] = $trail;
	}

	function actionDefault_list($form_name='') {
		$current = $this->trail->current();
		$this->session->set('patient_action', $current->link());
		
		return $this->actionList($form_name);
	}
	
	
	/**##@+
	 * Alias to old menu entry 
	 *
	 * @todo  remove once menu is updated
	 */
	function actionFind($form_name = '') {
		return $this->actionList($form_name);
	}
	
	function processFind() {
		return $this->processList();
	}
	/**##@-*/
	
	
	/**
	* Function that will display a patient finder widged, allowing
	*	the user to input search parameters to find a patient id.
	*/
	function actionList($form_name='') {
		$current = $this->trail->current();
		$this->session->set('patient_action', $current->link());
		
		//fix any magic quotes meddling
		if (get_magic_quotes_gpc()) {$form_name = stripslashes($form_name);}
		
		//prevent javascript injection, whitespace and semi-colons are the worry
		$form_name = preg_replace("/[^A-Za-z0-9\[\]\_\']/iS","",urldecode($form_name));
		$this->assign('form_name', $form_name);
		$this->assign("FORM_ACTION", Celini::link('list',true,true,$form_name));
		$this->assign('PATIENT_ACTION',Celini::link('view','PatientDashboard',true));
		return $this->view->render("find.html");
	}
	
	function processList() {
		$search_string = $_POST['searchstring'];
		$result_array = $this->SmartSearch($search_string,$this->showNonPatients);

		$this->assign('search_string',$search_string);
		$this->assign('result_set', $result_array);
	}

	function SmartSearch($search_string,$showNonPatients=false) {
		$this->showNonPatients=$showNonPatients;
		$join_type = "INNER";
		if ($this->showNonPatients === true) {
			$join_type = "LEFT";
		}
		//get the db connection and pass it to the helper functions
		$userProfile =& Celini::getCurrentUserProfile();
		if (count($userProfile->getPracticeIdList()) > 0) {
			$practiceFiltering = '
				(
					psn.primary_practice_id IN(' . implode(", ", $userProfile->getPracticeIdList()) . ') OR
					secondary.practice_id IN(' . implode(', ', $userProfile->getPracticeIdList()) . ')
				) ';
		
		}
		else {
			$practiceFiltering = '1=1';
		}

		$sql = "
			SELECT 
				CONCAT(last_name, ', ', first_name, ' ', middle_name) as name,
				date_of_birth as DOB,
				psn.person_id as id,
				record_number as pubpid, 
				psn.identifier as ss,
				person_type, 
				CONCAT(last_name, ', ', first_name, ' ', middle_name, '#', record_number)  as `string`,
				address.line1,
				address.city,
				practices.name AS practice_name
			FROM
				person psn
				$join_type JOIN patient AS pt ON(psn.person_id=pt.person_id)
				LEFT JOIN person_type AS ptype ON(ptype.person_id=psn.person_id)
				LEFT JOIN secondary_practice AS secondary ON(psn.person_id = secondary.person_id)
				LEFT JOIN person_address pa ON(pa.person_id=pt.person_id)
				LEFT JOIN address ON(pa.address_id=address.address_id)
				LEFT JOIN practices ON(practices.id=psn.primary_practice_id)
			WHERE 
				{$practiceFiltering} AND ";

		$cleanedValue = mysql_real_escape_string($search_string);
		$sqls = $this->_smart_search($search_string);
		// var_dump($sqls);
		$sqland = $sql .'('. implode(' AND ',$sqls). ")
			GROUP BY record_number
			ORDER BY 
				(last_name = '$cleanedValue') DESC,
				(first_name = '$cleanedValue') DESC,
				last_name,
				first_name 
			LIMIT
				{$this->limit}";
		$sqlor = $sql .'('. implode(' OR ', $sqls) . ")
			GROUP BY record_number
			ORDER BY
				(last_name = '{$cleanedValue}') DESC,
				(first_name = '{$cleanedValue}') DESC,
				last_name,
				first_name 
			LIMIT {$this->limit}";
		if(count($sqls)==0){
			return(array('','Invalid Search'));
		}
		// print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sqland);
		if(count($result_array)==0){
			$andfailed=true;
			$result_array=$this->_db->GetAll($sqlor);
		}
		if ($this->showNonPatients) {
			$person =& ORDataObject::factory('Person');
			foreach($result_array as $key => $row) {
				if (empty($row['person_type'])) {
					$row['person_type'] = 1;
				}
				$result_array[$key]['person_type'] = $person->lookupType($row['person_type']);
				$result_array[$key]['string'] = $result_array[$key]['name'] .'('.$result_array[$key]['person_type'].')';
			}
		}
		
		return($result_array);
	}

	/**
	* Returns array of sql items to put in the WHERE clause
	* @param string $search_string space-separated list of items for smart search
	*
	* @todo Update the formatting of this so it matches the Uversa coding 
	*    standards (see wiki).
	* @todo Remove all ereg code and replace with preg_match
	* @todo Consider wholesale refractoring.  Each of this various if() 
	*    statements contain code that could possibly be (or maybe is being) used
	*    elsewhere in CH.  A perfect example is the date checking.  All of that
	*    is already handled in DateObject, so there's no need to do it all again
	*    (now using dateobject)
	* @todo Determine if all of the if()s should be mutually exclusive, or
	*    should you attempt to match as many as you can guess
	*/
	function _smart_search($search_string){
		$GLOBALS['namesearch']=false;
		//var_dump($search_string);
		if (preg_match('/([a-z0-9]+), ?([a-z0-9]+)/i', $search_string, $matches)) {
			$searcharray = $matches;
			array_shift($searcharray);
		}
		else {
			$searcharray=explode(" ",$search_string);
			array_unshift($searcharray,$search_string);
		}
		$xdate=&new DateObject();
		for($x=0;$x<count($searcharray);$x++){
			$searcharray[$x]=trim($searcharray[$x]);
			$xdate=$xdate->create($searcharray[$x]);
			// Special sql for name-name if not a date or ssn
			if(
			   strpos($searcharray[$x],'-')!==FALSE 
			   && !ereg('^[0-9].*',$searcharray[$x]) // match date,ssn,etc
				){
				$GLOBALS['namesearch']=true;
				$searcharray[$x]=mysql_real_escape_string($searcharray[$x]);
				$search=explode("-",$searcharray[$x]);
				$sqls[]="(last_name LIKE '".$search[0]."-%".$search[1]."' OR last_name LIKE '".$search[0]."-".$search[1]."%' OR last_name LIKE '".$searcharray[$x]."-%' OR last_name LIKE '%-".$searcharray[$x]."' OR last_name = '".$search[0]."' OR last_name = '".$search[1]."')\n";
			} elseif($xdate->isValid()){
			// Date of birth
				$sqls[]="date_of_birth = '".$xdate->toISO()."'";
			} elseif(ereg('^([0-9]{3})\-?([0-9]{2})\-?([0-9]{4})$',$searcharray[$x],$date)){
			// SSN
				list($date,$a,$b,$c)=$date;
				$sqls[]="(identifier='$a-$b-$c' OR identifier='$a$b$c')";
			}
			// internal ID
			elseif (preg_match('/^[0-9]+$/', $searcharray[$x])) {
				$sqls[] = "record_number = '" . (int)$searcharray[$x]."'";
			} else {
			// Regular name
				$GLOBALS['namesearch']=true;
				$cleanedValue = mysql_real_escape_string($searcharray[$x]);
				$cleanedValue = str_replace(array(',', ' '), '', $cleanedValue);
				$sqls[]="(first_name LIKE '".$cleanedValue."%' OR last_name LIKE '".$cleanedValue."%' OR last_name LIKE '%-".$cleanedValue."%')";
			}
		}
		return($sqls);
	}

}
?>
