<?php
/**
 * Object Relational Persistence Mapping Class for table: visit_queue_template
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class VisitQueueTemplate extends ORDataObject {

	/**#@+
	 * Fields of table: visit_queue_template mapped to class members
	 */
	var $visit_queue_template_id	= '';
	var $number_of_appointments		= '';
	var $visit_queue_reason_id		= '';
	var $visit_queue_rule_id		= ''; // for possible future use
	var $title						= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'visit_queue_template';

	/**
	 * Primary Key
	 */
	var $_key = 'visit_queue_template_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'VisitQueueTemplate';

	/**
	 * Handle instantiation
	 */
	function VisitQueueTemplate() {
		parent::ORDataObject();
	}
	
	function isBeingUsed() {
		$qs =& $this->getChildren('VisitQueue');
		if($qs->count() > 0) {
			return true;
		}
		return false;
	}
	
	function &getReasons() {
		$r =& Celini::newORDO('VisitQueueReason');
		$finder =& $r->relationshipFinder();
		$finder->setParent($this);
		$finder->_orderBy = 'visit_queue_reason.ordernum ASC';
		$r =& $finder->find();
		return $r;
	}

	
}
?>