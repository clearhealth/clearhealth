<?php
require_once CELLINI_ROOT . "/controllers/Controller.class.php";

class C_PatientFinder extends Controller {

	var $template_mod;
	var $_db;
	var $join_db;
	var $limit;
	
	function C_PatientFinder($template_mod = "general") {
		parent::Controller();
		$this->_db = $GLOBALS['frame']['adodb']['db']; 
		//$this->_join_db = $GLOBALS['frame']['config']['openemr_db'];
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", Cellini::link(true,true,false) . $_SERVER['QUERY_STRING']);
		$this->assign("CURRENT_ACTION", Cellini::link('patient_finder'));
		$this->limit = 50;
		
		//remove the trail entries for this because it is a popup
		$trail = $_SESSION['trail'];
		if(is_array($trail)) array_shift($trail);
		$_SESSION['trail'] = $trail;
	}

	function default_action($form_name='') {
		return $this->find_action($form_name);
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
		$this->assign("FORM_ACTION", Cellini::link('find',true,false,$form_name));
		return $this->fetch($GLOBALS['template_dir'] . "patient_finder/" . $this->template_mod . "_find.html");
	}
	
	/**
	* Function that will take a search string, parse it out and return all patients from the db matching.
	* @param string $search_string - String from html form giving us our search parameters
	*/
	function find_action_process() {
		if ($_POST['process'] != "true")
			return;
		$search_string = $_POST['searchstring'];
		//get the db connection and pass it to the helper functions
		$sql = "SELECT CONCAT(last_name, ', ', first_name, ' ', middle_name) as name, date_of_birth as DOB, psn.person_id as id, record_number as pubpid, psn.identifier as ss FROM patient pt"
		." LEFT JOIN person as psn on psn.person_id=pt.person_id ";
		//parse search_string to determine what type of search we have
		$pos = strpos($search_string, ',');
		
		// get result set into array and pass to array
		$result_array = array();
		if (preg_match("/[0-9]{3}-[0-9]{2}-[0-9]{4}/",$search_string)) {
			$result_array = $this->search_by_ssn($sql, $search_string);
		}
		elseif (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}/",$search_string)) {
			$result_array = $this->search_by_dob($sql, $search_string);
		}
		elseif (is_numeric($search_string)) {
			$result_array = $this->search_by_number($sql, $search_string);
		}
		else if($pos === false) {
			//no comma just last name
			$result_array = $this->search_by_lName($sql, $search_string);
		}
		else if($pos === 0){
			//first name only
			$result_array = $this->search_by_fName($sql, $search_string);
		}
		else {
			//last and first at least
			$result_array = $this->search_by_FullName($sql,$search_string);
		}
		$this->assign('search_string',$search_string);
		$this->assign('result_set', $result_array);
		// we're done
		$_POST['process'] = "";
	}

	function find_remoting($search_string) {
		$_POST['process'] = true;
		$_POST['searchstring'] = $search_string;
		$this->find_action_process();
		if (is_array($this->_tpl_vars['result_set'])) return $this->_tpl_vars['result_set'];
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
		$sql .= " WHERE pubpid = '$number'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		echo $sql;
		//print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sql);
		//print_r($result_array);
		return $result_array;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a search by SSN
	*	@-param string $sql base sql query
	*	@-param string $search_string parsed for SSN
	*/
	function search_by_ssn($sql, $search_string) {
		$number = mysql_real_escape_string($search_string);
		$sql .= " WHERE ss = '$number'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		echo $sql;
		//print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sql);
		//print_r($result_array);
		return $result_array;
	}
	
	/**
	*	Function that returns an array containing the 
	*	Results of a search by DOB
	*	@-param string $sql base sql query
	*	@-param string $search_string parsed for DOB
	*/
	function search_by_dob($sql, $search_string) {
		$dob = mysql_real_escape_string($search_string);
		$dob = date("Y-m-d",strtotime($search_string));
		$sql .= " WHERE DOB = '$dob'" . " ORDER BY last_name, first_name";
		$sql .= " LIMIT " . $this->limit;
		echo $sql;
		//print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sql);
		return $result_array;
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
		echo $sql;
		//print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sql);
		//print_r($result_array);
		return $result_array;
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
		$sql .= " WHERE fname LIKE '$fName%'" . " ORDER BY first_name";
		$sql .= " LIMIT " . $this->limit;
		echo $sql;
		$result_array = $this->_db->GetAll($sql);
		return $result_array;
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
		echo $sql;
		//print "SQL is $sql \n";
		$result_array = $this->_db->GetAll($sql);
		return $result_array;
	}
}
?>
