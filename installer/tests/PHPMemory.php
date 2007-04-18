<?php
/*
 * PHPMemory
 *
 * Tests that the PHP Memory configuration is >= the supplied parameter
 */
 
class PHPMemory extends BaseTest{

	function PHPMemory($params){
		parent::BaseTest($params);	

		if(!is_array($this->params) || count($this->params) <= 0){
			ErrorStack::addError("Invalid parameters, memory to test against must be supplied as the only item in an array", ERRORSTACK_ERROR, 'PHPMemory');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
	}
	
	function perform(){	
	
		if($this->return_bytes($this->params[0]) < $this->return_bytes(ini_get('memory_limit'))){
			$this->result_message = "PHP Memory {$this->params[0]} or greater is required, you are running ".ini_get('memory_limit');
			$this->result = INSTALLER_TEST_SUCCESS;
			return $this->result;
		}else{
			$this->result = INSTALLER_TEST_FAIL;
			$this->result_message = "PHP Memory {$this->params[0]} or greater is required, you are running ".ini_get('memory_limit');
		}
		
		return $this->result;

	}

	//copied from php documentation on ini_get...
	function return_bytes($val) {
	   $val = trim($val);
	   if (empty($val)) {
	   	$val = '4G';
	   }
	   $last = strtolower($val{strlen($val)-1});
	   switch($last) {
	       // The 'G' modifier is available since PHP 5.1.0
		case 'g':
  	        	$val *= 1024;
		case 'm':
	        	$val *= 1024;
  		case 'k':
         		$val *= 1024;
   		}

   	return $val;
	}

}
?>
