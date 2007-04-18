<?php
/*
 * PHPVersionOver
 *
 * Tests that the running PHP Version is >= the supplied parameter
 */
 
class PHPVersionOver extends BaseTest{

	function PHPVersionOver($params){
		parent::BaseTest($params);	

		if(!is_array($this->params) || count($this->params) <= 0){
			ErrorStack::addError("Invalid parameters, version to test against must be supplied as the only item in an array", ERRORSTACK_ERROR, 'PHPVersionOver');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
	}
	
	function perform(){		
		if(version_compare(phpversion(), $this->params[0], '>=')){
			$this->result_message = "PHP Version {$this->params[0]} or greater is required, you are running ".phpversion();
			$this->result = INSTALLER_TEST_SUCCESS;
			return $this->result;
		}else{
			$this->result = INSTALLER_TEST_FAIL;
			$this->result_message = "PHP Version {$this->params[0]} or greater is required, you are running ".phpversion();
		}
		
		return $this->result;
	}
}
?>
