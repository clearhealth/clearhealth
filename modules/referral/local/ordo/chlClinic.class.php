<?php

class chlClinic extends ORDataObject
{
	var $clinic_id = '';
	var $clinic_id_string = '';
	var $group_name = '';
	var $name = '';
	var $full_name = '';
	
	var $_table = 'buildings';
	function chlClinic() {
		parent::ORDataObject();
		
		$this->_table = "buildings";
	}
	
	function setupByIdString($string) {
		$this->set('clinic_id_string', $string);
		parent::populate('clinic_id_string');
	}
	
	function populate() {
		parent::populate('clinic_id');
	}
	
	function get_id() {
		return $this->get('clinic_id');
	}
	
	function set_id($value) {
		$this->set('clinic_id', $value);
	}
}

