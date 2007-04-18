<?php

$GLOBALS['loader']->requireOnce('includes/FilterBase.class.php');

class Filter_Suggest extends FilterBase{
	var $params = array('person' => false, 'jsfunc' => '');

	function Filter_Suggest($name, $label, $value = null, $params = null){
		parent::FilterBase($name, $label, $value, $params);
	}
	
	function getHTML($options){
		$head =& Celini::HTMLHeadInstance();
		$head->addJs('scriptaculous');
		$head->addExternalCss('suggest');
		$this->view->assign('name', $this->name);
		$this->view->assign('label', $this->label);
		$this->view->assign('value', $this->value);
		$this->view->assign('params',$this->params);
		return $this->view->fetch('filter/general_suggest.html');
	}
	
	function getSettingsHTML(){
		$value = 'Not Set';
		$id = '';
		if(!empty($this->value)){
			$value = $this->value['value'];
			$id = $this->value['id'];
		}

		return "<DIV ID='filter_{$this->name}'>{$this->label}: $value ($id)</DIV>";
	}
}
?>