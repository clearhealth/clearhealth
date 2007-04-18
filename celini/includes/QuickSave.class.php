<?php

class QuickSave {
	var $session;
	var $formdata;
	function QuickSave() {
		$this->session =& Celini::sessionInstance();
		$this->formdata = $this->session->get('formData',array());
	}
	
	function saveForm($form) {
		list($formid,$formIdentifier,$data) = $form;
		$this->formdata[$formid] = array($formIdentifier => array());
		foreach($data as $input) {
			$this->formdata[$formid][$formIdentifier][$input[0]]=array($input[1],$input[2]);
		}
		$this->_saveSession();
	}
	
	function loadForm($formid,$formIdentifier) {
		if(isset($this->formdata[$formid]) && isset($this->formdata[$formid][$formIdentifier])) {
			return array($formid,$this->formdata[$formid][$formIdentifier]);
		}
		return false;
	}
	
	function saveItem($form) {
		list($formid,$formIdentifier,$data) = $form;
		if(!isset($this->formdata[$formid])) {
			$this->formdata[$formid] = array($formIdentifier=>array(array()));
		}
		if(!isset($this->formdata[$formid][$formIdentifier])) {
			$this->formdata[$formid] = array(array());
		}
		$this->formdata[$formid][$formIdentifier][$data[0]]=array($data[1],$data[2]);
		$this->_saveSession();
	}
	
	function _saveSession() {
		$this->session->set('formData',$this->formdata);
	}
	
	function dump() {
		var_dump($this->formdata);
	}
	
	function clearForm($formid,$formIdentifier=0) {
		$session =& $this->_getSession();
		$formdata = $session->get('formData');
		if(isset($formdata[$formid]) && isset($formdata[$formid][$formIdentifier])) {
			unset($formdata[$formid][$formIdentifier]);
			$session->set('formData',$formdata);
		}
	}
}

?>
