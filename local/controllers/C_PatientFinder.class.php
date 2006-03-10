<?php
require_once CELINI_ROOT . "/controllers/Controller.class.php";

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

	function actionDefault($form_name='') {
		return $this->smartsearch_action($form_name);
	}
	
	/**
	* Function that will display a patient finder widged, allowing
	*	the user to input search parameters to find a patient id.
	*/
	function find_action($form_name='') {
		
		//fix any magic quotes meddling
		if (get_magic_quotes_gpc()) {$form_name = stripslashes($form_name);}
		
        //prevent javascript injection, whitespace and semi-colons are the worry
        $form_name = preg_replace("/[^A-Za-z0-9\[\]\_\']/iS","",urldecode($form_name));
		$this->assign('form_name', $form_name);
		$this->assign("FORM_ACTION", Celini::link('find',true,true,$form_name));
		$this->assign('PATIENT_ACTION',Celini::link('view','PatientDashboard',true));
		return $this->view->render("find.html");
	}
	
	/**
	* Function that will take a search string, parse it out and return all patients from the db matching.
	* @param string $search_string - String from html form giving us our search parameters
	*/
	function find_action_process() {
		if ($_POST['process'] != "true")
			return;
		$search_string = $_POST['searchstring'];
		$join_type = "INNER";
		if ($this->showNonPatients === true) {
			$join_type = "LEFT";
		}
		//get the db connection and pass it to the helper functions
		$sql = "SELECT CONCAT(last_name, ', ', first_name, ' ', middle_name) as name, date_of_birth as DOB, psn.person_id as id, record_number as pubpid, psn.identifier as ss, person_type, concat(last_name, ', ', first_name, ' ', middle_name, '#', record_number)  as `string` FROM person psn"
		." $join_type JOIN patient as pt on psn.person_id=pt.person_id left join person_type ptype using(person_id)";
		//parse search_string to determine what type of search we have
		$pos = strpos($search_string, ',');
		
		// get result set into array and pass to array
		if (preg_match("/[0-9]{9}/",$search_string)) {
			$sql = $this->search_by_ssn($sql, $search_string);
		}
		elseif (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}/",$search_string)) {
			$sql = $this->search_by_dob($sql, $search_string);
		}
		elseif (is_numeric($search_string)) {
			$sql = $this->search_by_number($sql, $search_string);
		}
		else if($pos === false) {
			//no comma just last name
			$sql = $this->search_by_lName($sql, $search_string);
		}
		else if($pos === 0){
			//first name only
			$sql = $this->search_by_fName($sql, $search_string);
		}
		else {
			//last and first at least
			$sql = $this->search_by_FullName($sql,$search_string);
		}

		if(!isset($_POST['search_inactive']) || $_POST['search_inactive'] != 1){
			$sql = str_replace('WHERE', 'WHERE inactive = 0 AND', $sql);
		}
		
		//print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sql);

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
		$this->assign('search_string',$search_string);
		$this->assign('result_set', $result_array);
		// we're done
		$_POST['process'] = "";
	}

	function find_remoting($search_string,$showNonPatients = false) {
		if(empty($search_string)) return array();
		$search = $this->SmartSearch($search_string,$showNonPatients);
		if(count($search)>0){
			return $search;
		} else {
			return null;
		}
		$this->showNonPatients = $showNonPatients;
		$_POST['process'] = true;
		$_POST['searchstring'] = $search_string;
		
		$this->find_action_process();
		
		if (is_array($this->get_template_vars('result_set'))) {
			return $this->get_template_vars('result_set');
		}
		return null;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a Patient number search
	*	@-param string $sql base sql query
	*	@-param string $search_string parsed for patient number
	*/
	function search_by_number($sql, $search_string) {
		$number = mysql_real_escape_string($search_string);
		$sql .= " WHERE record_number = '$number'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		return $sql;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a search by SSN
	*	@-param string $sql base sql query
	*	@-param string $search_string parsed for SSN
	*/
	function search_by_ssn($sql, $search_string) {
		$number = mysql_real_escape_string($search_string);
		$sql .= " WHERE identifier = '$number'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		return $sql;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a search by DOB
	*	@-param string $sql base sql query
	*	@-param string $search_string parsed for DOB
	*/
	function search_by_dob($sql, $search_string) {
		$dob = mysql_real_escape_string($search_string);
		$doba = split("/",$search_string);
        $dob = $doba[2] . "-" . $doba[0] . "-" . $doba[1];
		$sql .= " WHERE date_of_birth = '$dob'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		return $sql;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a LastName search
	*	@-param string $sql base sql query
	*	@-param string $search_string parsed for last name
	*/
	function search_by_lName($sql, $search_string) {
		$lName = mysql_real_escape_string($search_string);
		$sql .= " WHERE last_name LIKE '$lName%'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		return $sql;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a FirstName search
	*	@param string $sql base sql query
	*	@param string $search_string parsed for first name
	*/
	function search_by_fName($sql, $search_string) {
		$name_array = split(",", $search_string);
		$fName = mysql_real_escape_string( trim($name_array[1]) );
		$sql .= " WHERE first_name LIKE '$fName%'" . " ORDER BY first_name";
		$sql .= " LIMIT " . $this->limit;
		return $sql;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a Full Name search
	*	@param string $sql base sql query
	*	@param string $search_string parsed for first, last and middle name
	*/
	function search_by_FullName($sql, $search_string) {
		$name_array = split(",", $search_string);
		$lName = mysql_real_escape_string($name_array[0]);
		$fName = mysql_real_escape_string( trim($name_array[1]) );
		$sql .= " WHERE first_name LIKE '%$fName%' AND last_name LIKE '$lName%'"  . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		//print "SQL is $sql \n";
		return $sql;
	}


	/**
	* Function that will display a patient finder widged, allowing
	*	the user to input search parameters to find a patient id.
	*/
	function smartsearch_action($form_name='') {
		
		//fix any magic quotes meddling
		if (get_magic_quotes_gpc()) {$form_name = stripslashes($form_name);}
		
        //prevent javascript injection, whitespace and semi-colons are the worry
        $form_name = preg_replace("/[^A-Za-z0-9\[\]\_\']/iS","",urldecode($form_name));
		$this->assign('form_name', $form_name);
		$this->assign("FORM_ACTION", Celini::link('smartsearch',true,true,$form_name));
		$this->assign('PATIENT_ACTION',Celini::link('view','PatientDashboard',true));
		return $this->view->render("find.html");
	}
	
	function smartsearch_action_process() {
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
		$sql = "SELECT CONCAT(last_name, ', ', first_name, ' ', middle_name) as name, date_of_birth as DOB, psn.person_id as id, record_number as pubpid, psn.identifier as ss, person_type, concat(last_name, ', ', first_name, ' ', middle_name, '#', record_number)  as `string` FROM person psn"
		." $join_type JOIN patient as pt on psn.person_id=pt.person_id left join person_type ptype using(person_id)\nWHERE\n";

		$sqls = $this->_smart_search($search_string);
		// var_dump($sqls);
		$cleanedValue = mysql_real_escape_string($search_string);
		$sqland=$sql.implode(' AND ',$sqls)." ORDER BY (last_name = '$cleanedValue') DESC, (first_name = '$cleanedValue') DESC, last_name,first_name LIMIT $this->limit";
		$sqlor=$sql.implode(' OR ',$sqls)." ORDER BY (last_name = '$cleanedValue') DESC, (first_name = '$cleanedValue') DESC, last_name,first_name LIMIT $this->limit";

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
