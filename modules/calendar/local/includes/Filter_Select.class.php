<?php

$GLOBALS['loader']->requireOnce('includes/FilterBase.class.php');

class Filter_Select extends FilterBase{

	function Filter_Select($name, $label, $value = null){
		parent::FilterBase($name, $label, $value);
	}
	
	function getHTML($options = null){
		$this->view->assign('name', $this->name);
		$this->view->assign('label', $this->label);
		$this->view->assign('value', $this->value);
		$this->view->assign('options', $options);
		return $this->view->fetch('filter/general_select.html');
	}

	function getSettingsHTML(){
		$setting = 'Not Set';
		if(!empty($this->value)){
			$setting = $this->value;
		}
		
		return "<DIV ID='filter_{$this->name}'>{$this->label}: $setting</DIV>";
	}
}
?>
