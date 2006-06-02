<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_data
 *
 * @package	com.uversainc.clearhealth
 */
class FeeScheduleData extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_data mapped to class members
	 */
	var $code_id		= '';
	var $revision_id	= '';
	var $fee_schedule_id	= '';
	var $data		= '';
	var $formula		= '';
	var $mapped_code	= '';
	/**#@-*/
	var $_table = 'fee_schedule_data';
	var $_internalName='FeeScheduleData';
	var $_key = 'code_id';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FeeScheduleData($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Fee_schedule_data with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	function setupByCodeFeeSchedule($code_id,$fee_schedule_id) {
		$c = EnforceType::int($code_id);
		$f = EnforceType::int($fee_schedule_id);

		$sql = "select * from ".$this->tableName()." where code_id = $c and fee_schedule_id = $f";
		$this->helper->populateFromQuery($this,$sql);
		$this->set('code_id',$c);
		$this->set('fee_schedule_id',$f);
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('code_id');
	}

	/**#@+
	 * Getters and Setters for Table: fee_schedule_data
	 */

	
	function get_revision_id() {
		return 0;
	}

	/**#@-*/
}
?>
