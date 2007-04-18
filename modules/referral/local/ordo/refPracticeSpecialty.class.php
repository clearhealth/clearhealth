<?php

require_once CELINI_ROOT . '/ordo/ORDataObject.class.php';

/**
 * @todo This needs to be integrated with our existing form object
 */ 
class refPracticeSpecialty extends ORDataObject
{
	var $refpractice_specialty_id = '';
	var $specialty = '';
	var $form = '';
	var $refpractice_id = '';
	
	var $_table = 'refpractice_specialty';
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id', (int)$id);
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate('refpractice_specialty_id');
	}
	
	function get_id() {
		return $this->get('refpractice_specialty_id');
	}
	
	function set_id($value) {
		$this->set('refpractice_specialty_id', $value);
	}
}

