<?php
/**
 * Object Relational Persistence Mapping Class for table: visit_queue_reason
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class VisitQueueReason extends ORDataObject {

	/**#@+
	 * Fields of table: visit_queue_reason mapped to class members
	 */
	var $visit_queue_reason_id		= '';
	var $ordernum		= '';
	var $appt_length = '';
	var $reason		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'visit_queue_reason';

	/**
	 * Primary Key
	 */
	var $_key = 'visit_queue_reason_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'VisitQueueReason';

	/**
	 * Handle instantiation
	 */
	function VisitQueueReason() {
		parent::ORDataObject();
	}

	function get_length() {
		$l = $this->get('appt_length');
		return date('H:i',strtotime($l));
	}
	
	function &getAppointmentForQueue(&$visitq) {
		$finder =& $this->relationshipFinder();
		$finder->setRelatedType('Appointment');
		$finder->addParent($this);
		$finder->addParent($visitq);
		$apts = $finder->find();
		if($apts->count() == 0) {
			return false;
		}
		$apt =& $apts->current();
		return $apt;
	}
	
}
?>