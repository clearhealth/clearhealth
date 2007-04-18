<?php

$GLOBALS['loader']->requireOnce('includes/FilterBase.class.php');
class Filter_Multiselect extends FilterBase{
	var $params = array('size' => 3, 'type' => 'ajax', 'insertBlank' => false, 'options' => array());
	var $value = array();

	function Filter_Multiselect($name, $label, $value = null, $params = null){
		parent::FilterBase($name, $label, $value, $params);
	}
	
	function getHTML($options = null){
		if(is_null($options)) {
			$options = $this->params['options'];
		}
		$this->view->assign('name', $this->name);
		$this->view->assign('label', $this->label);
		$this->view->assign('values', $this->value);
		$this->view->assign('options', $options);
		$this->view->assign('params',$this->params);
		return $this->view->fetch('filter/general_multiselect.html');
	}

	function getSettingsHTML(){
		if($this->params['type'] == 'ajax' && count($this->value) > 0) {
			$this->view->assign('name', $this->name);
			$this->view->assign('label', $this->label);
			$this->view->assign('values', $this->value);
			return $this->view->fetch('filter/general_multiselect_setting.html');	
		}
		return '';
	}
	
	function clearValue(){
		$this->value = array();
	}

	function getValue(){
		return $this->value;
	}
	
	function setValue($value){
		if($this->params['type'] !== 'ajax') {
			$this->value = array();
			if(is_array($value)) {
				foreach($value as $val) {
					if($val == '') { // They selected Clear Filters
						$this->value = array();
						return;
					}
					$this->value[$val] = $val;
				} 
			}
		} else { // Using ajax method
			list($value,$label) = split('-', $value);
			$this->value[$value] = $label;
		}
	}
	
	function removeValue($value){
		list($value,$label) = split('-', $value);
		if(isset($this->value[$value])) unset($this->value[$value]);
	}
}
?>