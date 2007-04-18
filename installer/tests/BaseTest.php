<?php
define('INSTALLER_TEST_FAIL', 1);
define('INSTALLER_TEST_WARNING', 2);
define('INSTALLER_TEST_SUCCESS', 4);
define('INSTALLER_TEST_UNKNOWN', 8);

/*
 * BaseTest Class
 *
 * This is an abstract class that needs to have at
 * least the perform method overridden
 */
class BaseTest {
	var $result;
	
	var $result_message;
	
	var $params;
	
	function BaseTest($params){
		$this->result = INSTALLER_TEST_UNKNOWN;
		$this->result_message = 'Test not yet ran!';
		$this->params = $params;
	}
	
	/*
	 * This function needs to be overriden in the implementing class
	 * and should return one of the following defined constants.
	 * 
	 * INSTALLER_TEST_FAIL
	 * INSTALLER_TEST_WARNING
	 * INSTALLER_TEST_SUCCESS
	 * INSTALLER_TEST_UNKNOWN
	 * 
	 * @var $params array Array of parameters needed for the specific implementation
	 */
	function perform(){
		return INSTALLER_TEST_FAIL;	
	}
	
	function getResult(){
		return $this->result;	
	}
	
	function getResultMessage(){
		return $this->result_message;	
	}

	function success(){
		if ($this->result == INSTALLER_TEST_SUCCESS || $this->result == INSTALLER_TEST_WARNING) {
			return true;
	}
		return false;
	}
}
?>
