<?php
/*
 * ErrorStack class
 *
 * This class will help to manage all error messages
 * reported in the application
 */

define('ERRORSTACK_ERROR', 1);
define('ERRORSTACK_WARNING', 2);
define('ERRORSTACK_INFO', 4);
define('ERRORSTACK_DEBUG', 8);
define('ERRORSTACK_FATAL', 16);

class ErrorStack{
	
	function ErrorStack(){
	}
	
	function addError($message, $level = ERRORSTACK_ERROR, $class_name = ''){
		if(!isset($GLOBALS['INSTALLER']['ERROR_STACK'])){
			$GLOBALS['INSTALLER']['ERROR_STACK'] = array();
		}
		
		$error =  new Error($message, $level, $class_name);
		$GLOBALS['INSTALLER']['ERROR_STACK'][] =& $error;
		
		if($level == ERRORSTACK_FATAL){
			die($error->toString());	
		}
	}
	
	function errorsExist(){
		if(isset($GLOBALS['INSTALLER']['ERROR_STACK']) && count($GLOBALS['INSTALLER']['ERROR_STACK'] > 0)){
			return TRUE;
		}
		
		return FALSE;	
	}
	
	function levelToString($level){
		$level_string = 'UNKNOWN';
		if($level == ERRORSTACK_ERROR){
			$level_string = 'ERROR';
		}elseif($level == ERRORSTACK_WARNING){
			$level_string = 'WARNING';
		}elseif($level == ERRORSTACK_INFO){
			$level_string = 'INFO';
		}elseif($level == ERRORSTACK_DEBUG){
			$level_string = 'DEBUG';
		}elseif($level == ERRORSTACK_FATAL){
			$level_string = 'FATAL';
		}
		
		return $level_string;
	}
	
	function errorsAsHTML(){
		$html = '';
		
		if(ErrorStack::errorsExist()){
			foreach($GLOBALS['INSTALLER']['ERROR_STACK'] as $error){
				$html .= $error->toString()."<BR>\n";	
			}	
		}
		
		return $html;			
	}
}

class Error{
	
	var $message = '';
	
	var $level = ERRORSTACK_ERROR;
	
	var $class_name = '';
	
	function Error($message, $level, $class_name){
		$this->message = $message;
		$this->level = $level;
		$this->class_name = $class_name;
	}
	
	function toString(){
		$string = '';
		if(!empty($this->class_name)){
			$string = ErrorStack::levelToString($this->level).": $this->class_name: $this->message";
		}else{
			$string = ErrorStack::levelToString($this->level).": $this->message";
		}
		
		return $string;
	}
}
?>
