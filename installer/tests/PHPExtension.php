<?php
/*
 * PHPExtension
 *
 * Tests that the extensions passed in as parameters
 * are loaded into the running PHP instance
 */
 
class PHPExtension extends BaseTest{

	function PHPExtension($params){
		parent::BaseTest($params);	

		if(!is_array($this->params) || count($this->params) <= 0){
			ErrorStack::addError("Invalid parameters, you must provide at least one extension to test for", ERRORSTACK_ERROR, 'PHPExtension');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
	}
	
	function perform(){
		$this->result_message = "Found extensions: ".join(', ', $this->params);
		$this->result = INSTALLER_TEST_SUCCESS;
		foreach($this->params as $extension){
			if(!extension_loaded($extension)){
				$this->result = INSTALLER_TEST_FAIL;
				$this->result_message = "PHP Extension $extension is not loaded";
			}
		}
		
		return $this->result;
	}
}
?>
