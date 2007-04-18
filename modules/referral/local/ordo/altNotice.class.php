<?php

/**
 * Provides an ordo for Alert system Notices
 *
 * This is prefixed "alt" so it can be removed into its own module at some point
 * in the future and exist independent of the referral module.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.uversainc.alerts
 */
class altNotice extends ORDataObject
{
	// values
	var $altnotice_id  = '';
	var $creation_date = '';
	var $due_date      = '';
	var $title         = '';
	var $note          = '';
	var $diagnosis     = '';
	var $completed_date = '';
	var $chlfollow_up_reason = '';
	var $clinic_id = '';
	
	/**
	 * The type of owner of this notice.
	 *
	 * This will normally refer to a type of ORDO.  It can, however, refer to 
	 * any number of hard coded values that your code will be checking for.  In
	 * Celini, we will be adding something along the lines of "ACL Group", which
	 * will tie a given notice to anyone in that group.
	 *
	 * @var string
	 */
	var $owner_type = '';
	
	
	/**
	 * The ID of the owner.
	 *
	 * In the case of a notice being owned by a particular ACL group, this could
	 * be a string.  Example: $owner_type == "ACL Group", 
	 * $owner_id = "Site Admin".
	 *
	 * @var int|string
	 */
	var $owner_id   = '';
	
	// external reference data
	var $external_type = '';
	var $external_id   = '';
	
	/**
	 * A flag denoting whether this note is "deleted" or not
	 *
	 * @var int
	 */
	var $deleted = '0';
	
	var $_table = 'altnotice';
	
	/**
	 * @todo Consider moving this to ORDataObject for simple tables to automate
	 *   the populate(), set_id(), and get_id() methods.
	 */
	var $_primaryKey = 'altnotice_id';
	
	function setup($id = 0) {
		parent::setup($id);
		if (!$this->isPopulated()) {
			$this->set('creation_date', date('Y-m-d H:i:s')); 
		}
	}
	
	function populate() {
		parent::populate($this->_primaryKey);
	}
	
	function get_id() {
		return $this->get($this->_primaryKey);
	}
	
	function set_id($value) {
		$this->set($this->_primaryKey, $value);
	}
	
	function get_due_date() {
		return $this->_getDate('due_date');
	}
	
	function set_due_date($value) {
		$this->_setDate('due_date', $value);
	}
	
	function get_creation_date() {
		return $this->_getDate('creation_date');
	}
	
	function set_creation_date($value) {
		$this->_setDate('creation_date', $value);
	}
	
	function get_completed_date() {
		return $this->_getDate('completed_date');
	}
	
	function set_completed_date($value) {
		$this->_setDate('completed_date', $value);
	}
	
	function get_title() {
		return empty($this->title) ? substr($this->get('note'), 0, 80) : $this->title;
	}
}

