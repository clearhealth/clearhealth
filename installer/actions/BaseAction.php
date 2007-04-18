<?php
define('INSTALLER_ACTION_FAIL', 1);
define('INSTALLER_ACTION_WARNING', 2);
define('INSTALLER_ACTION_SUCCESS', 4);
define('INSTALLER_ACTION_UNKNOWN', 8);

/*
 * BaseAction Class
 *
 * This is an abstract class that needs to have at
 * least the perform method overridden
 */
class BaseAction {
	var $result = INSTALLER_ACTION_FAIL;
	
	var $result_message = 'Test not yet ran.';
	
	var $params;
	
	var $interactive = false;

	var $grouping = true;
	
	function BaseAction($params){
		$this->params = $params;
	}
	
	/*
	 * This function needs to be overriden in the implementing class
	 * and should return either TRUE or FALSE.
	 * 
	 */
	function perform(){
		return FALSE;	
	}
	
	/*
	 * If the return value is TRUE then 
	 * the getHTML() and submitData() functions will be called.
	 */
	function isInteractive(){
		return $this->interactive;
	}
	
	/*
	 * This function needs to be overriden in the implementing class and
	 * should return the HTML used to collect data from the user.
	 */
	function getHTML(){
		return '';	
	}
	
	/*
	 * This function needs to be overriden in the implementing class and
	 * is called when the data has been submitted from the getHTML()
	 * function. This function must return TRUE for the installation
	 * process to continue.
	 */
	function dataSubmitted(){
		return TRUE;	
	}
	
	/*
	 * If the return value is FALSE then this action will be displayed 
	 * by itself and not be grouped with other actions.
	 */
	function allowGrouping(){
		return $this->grouping;
	}

	function getResult(){
		return $this->result;	
	}
	
	function getResultMessage(){
		return $this->result_message;	
	}

	function success(){
		if ($this->result === INSTALLER_ACTION_SUCCESS)
			return true;
		return false;
	}
}
?>
