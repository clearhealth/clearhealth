<?php
/*
 * WritableLocation
 *
 * Tests that the extensions passed in as parameters
 * are loaded into the running PHP instance
 */
 
class WritableLocation extends BaseTest{

	function WritableLocation($params){
		parent::BaseTest($params);	

		if(!is_array($this->params) || count($this->params) <= 0){
			ErrorStack::addError("Invalid parameters, you must provide at least one location to test.", ERRORSTACK_ERROR, 'PHPExtension');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
	}
	
	function perform(){
		if (count($this->params) > 1) {
			$this->result_message = "All files and locations writable: ".join(', ', $this->params);
		}
		else {
		$this->result_message = "File or location writable: ".join(', ', $this->params);
		}
		$this->result = INSTALLER_TEST_SUCCESS;
		foreach($this->params as $location){
			if(!is_writable($location)){
				$this->result = INSTALLER_TEST_FAIL;
				$this->result_message = "Can not write to $location!";
			}
		}
		
		return $this->result;
	}
}
?>
