<?php
/*
 * PHPMagicQuotes
 *
 * Tests that the PHP Magic quotes matches the parameter
 */
 
class PHPMagicQuotes extends BaseTest{


	var $desired_state;

	function PHPMagicQuotes($params){
		parent::BaseTest($params);

	
		if(is_array($this->params) || count($this->params) < 0){

			if($params[0] != "On" && $params[0] != "Off")
			ErrorStack::addError("Invalid parameters, must be On or Off", ERRORSTACK_ERROR, 'PHPMagicQuotes');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
	}
	
	function perform(){	
	
		if(get_magic_quotes_gpc()=="1"){
			$actual_state="On";
		}else{
			$actual_state="Off";
		}


		$this->desired_state = $this->params[0];

		if($actual_state==$this->desired_state){
			$this->result_message = "PHP Magic Quotes is ".$this->desired_state;
			$this->result = INSTALLER_TEST_SUCCESS;
			return $this->result;
		}else{
			$this->result = INSTALLER_TEST_WARNING;
			$this->result_message = "PHP Magic Quotes is $actual_state but it should be ".$this->desired_state;
		}
		
		return $this->result;

	}


}
?>
