<?php

class FilterBase {
	var $label = '';
	var $name = '';
	var $value = null;
	var $view = null;
	var $params = array();
	
	function FilterBase($name, $label, $value = null, $params = null) {
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
		$this->view = new clniView();
		$this->view->template_dir = APP_ROOT.'/modules/calendar/local/templates/';
		if(is_array($params)) {
			foreach($params as $key => $param) {
				$this->params[$key] = $param;
			}
		}
	}
    
	function getHTML($options = null){
	}
    
	function getSettingsHTML(){
	}
        
	function getName(){
		return $this->name;
	}
    
	function getLabel(){
		return $this->label;
	}
    
	function clearValue(){
		$this->value = null;
	}

	function getValue(){
		return $this->value;
	}
	
	function setValue($value){
		$this->value = $value;
	}
	
	function removeValue($value){
		$this->clearValue();
	}
}
?>