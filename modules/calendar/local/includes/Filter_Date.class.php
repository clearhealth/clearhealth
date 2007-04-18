<?php

$GLOBALS['loader']->requireOnce('includes/FilterBase.class.php');

class Filter_Date extends FilterBase{
	var $params = array('datetemplate'=>'%Y-%m-%d');

	function Filter_Date($name, $label, $value = null, $params = null){
		parent::FilterBase($name, $label, $value, $params);
	}
	
	function getHTML($options){
		$this->view->assign('name', $this->name);
		$this->view->assign('label', $this->label);
		$this->view->assign('value', $this->value);
		$this->view->assign('params',$this->params);
		return $this->view->fetch('filter/general_date.html');
	}
	
	function getSettingsHTML(){
		$date = 'Not Set';
		if(!empty($this->value)){
			$date = $this->value;
		}

		return "<DIV ID='filter_{$this->name}'>{$this->label}: $date</DIV>";
	}
}
?>