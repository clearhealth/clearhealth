<?php
/*
 * BaseSet class
 *
 * A type sensitive Set implementation for PHP
 * 
 */
 
class BaseSet{

	var $items;
	
	var $type;
	
	var $current_index;
	
	var $errors;
	
	function BaseSet($type){
		$this->items = array();
		$this->current_index = 0;
		if(class_exists($type)){
			$this->type = $type;
		}else{
			die("Class $type is not defined!");	
		}
		
		$this->errors = array();	
	}
	
	function add($item){
		if(is_a($item, $this->type)){
			$this->items[] =& $item;	
		}
	}
	
	function removeAt($index){
		if(isset($this->items[$index])){
			unset($this->items[$index]);
			$this->_compressArray();
		}
	}
	
	function _compressArray(){
		$item_count = count($this->items);
		$new_items = array();
		foreach($this->items as $item){
			$new_items[] =& $item;
		}
		$this->items =& $new_items;		
	}
	
	function &get(){
		if(isset($this->items[$this->current_index])){
			return $this->items[$this->current_index++];
		}
		
		$return = FALSE;
		return $return;
	}
	
	function reset(){
		$this->current_index = 0;
	}
	
	function _addError($message){
		$this->errors[] = $message;
	}
	
	function getErrors(){
		return $this->errors;
	}
	
	function getErrorsHTML(){
		$html = '';
		
		if(is_array($this->errors) && count($this->errors) > 0){
			foreach($this->errors as $error){
				$html .= $error."<BR>\n";	
			}	
		}
		
		return $html;
	}

	function clearErrors(){
		$this->errors = array();	
	}
	
}
?>
