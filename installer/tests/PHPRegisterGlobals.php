<?php
/*
 * PHPRegisterGlobals
 *
 * Tests that the PHP Register Globals matches the parameter
 */
 
class PHPRegisterGlobals extends BaseTest{


	var $desired_state;

	function PHPRegisterGlobals($params){
		parent::BaseTest($params);

	
		if(!is_array($this->params) || count($this->params) <= 0){


			if($params[0] != "On" && $params[0] != "Off")
			ErrorStack::addError("Invalid parameters, must be On or Off", ERRORSTACK_ERROR, 'PHPRegisterGlobals');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
	}
	
	function perform(){	
	
		if(ini_get('register_globals')=="1"){
			$actual_state="On";
		}else{
			$actual_state="Off";
		}


		$this->desired_state = $this->params[0];

	
		if($actual_state==$this->desired_state){
			$this->result_message = "PHP Register Globals is ".$this->desired_state;
			$this->result = INSTALLER_TEST_SUCCESS;
			return $this->result;
		}else{
			$this->result = INSTALLER_TEST_FAIL;
			$this->result_message = "PHP Register Globals is $actual_state but it should be ".$this->desired_state;
		}
		
		return $this->result;

	}


}
?>
