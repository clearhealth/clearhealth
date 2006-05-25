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
	
	function loadForm($formid,$formIdentifier,$clear=false) {
		if(isset($this->formdata[$formid]) && isset($this->formdata[$formid][$formIdentifier])) {
			$out = array($formid,$this->formdata[$formid][$formIdentifier]);
			if($clear==true) {
				$this->clearForm($formid,$formIdentifier);
			}
			return $out;
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
		$session =& $this->session;
		$formdata =& $this->formdata;
		if(isset($formdata[$formid]) && isset($formdata[$formid][$formIdentifier])) {
			unset($formdata[$formid][$formIdentifier]);
			$this->_saveSession();
		}
	}
}

?>
