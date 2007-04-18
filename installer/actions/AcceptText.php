<?php
/*
 * AcceptText Class
 *
 * This class accepts a filename of a license and then displays that license for acceptance by the user.
 *
 */
class AcceptText extends BaseAction {
	var $file_name;
	
	function AcceptText($params){
		parent::BaseAction($params);
		
		$this->interactive = true;
		$this->grouping = false;
		
		if(!is_array($params) || count($params) <= 0){
			$this->result = INSTALLER_TEST_FAIL;
			$this->result_message = "Invalid parameters passed to AcceptText action, filename is required.";	
			ErrorStack::addError($this->result_message, ERRORSTACK_ERROR, 'AcceptText');
		}else{
			$this->file_name = $params[0];
		}
	}
	
	/*
	 * This function returns the result value of the object
	 * 
	 */
	function perform(){
		return $this->result;	
	}
		
	/*
	 * This function accesses the file name and opens the file and places it the
	 * Smarty template for display
	 */
	function getHTML($smarty){
		$file_contents = 'Could not read file '.$this->file_name;
		if(is_readable($this->file_name)){
			$file_contents = file($this->file_name);
			$file_contents = join('', $file_contents);
		}
		
		$smarty->assign('FILE_CONTENTS', $file_contents);
		
		return $smarty->fetch(Installer::getTemplatePath('action_accept_text.tpl'));	
	}
	
	/*
	 * This function returns true (and allows installtion to continue)
	 * If the user has accepted the license text.
	 * 
	 */
	function dataSubmitted(){
		if($_POST['ACCEPT_TEXT_SUBMIT'] === "I Agree") {
			$this->result = INSTALLER_ACTION_SUCCESS;
			$this->result_message = "License Accepted";
			return true;
		}
		$this->result = INSTALLER_ACTION_FAIL;
		$this->result_message = "License NOT Accepted, You Cannot Continue.";
		return false;
	}
}
?>
