<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule
 *
 * @package	com.uversainc.clearheath
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
require_once CELLINI_ROOT.'/includes/Datasource_sql.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: fee_schedule
 *
 * @package	com.uversainc.clearhealth
 */
class FeeSchedule extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule mapped to class members
	 */
	var $id			= '';
	var $name		= '';
	var $label		= '';
	var $description	= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FeeSchedule($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'fee_schedule';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Fee_schedule with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * List all FeeScheduls
	 */
	function listFeeSchedules() {
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "fee_schedule_id, name, label, description",
				'from' 	=> "$this->_table f ",
				'orderby' => 'label'

			),
			array('label' => 'Name','description'=> 'Description'));
		return $ds;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('fee_schedule_id');
	}

	/**#@+
	 * Getters and Setters for Table: fee_schedule
	 */

	
	/**
	 * Getter for Primary Key: fee_schedule_id
	 */
	function get_fee_schedule_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: fee_schedule_id
	 */
	function set_fee_schedule_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	/**
	 * Returns an object that is the default fee schedule
	 */
	function &defaultFeeSchedule() {
		$feeSchedule =& ORDataObject::Factory('FeeSchedule');

		$res = $feeSchedule->_execute("select fee_schedule_id from $feeSchedule->_table limit 1");
		if ($res && isseT($res->fields['fee_schedule_id'])) {
			$feeSchedule->setup($res->fields['fee_schedule_id']);
		}
		return $feeSchedule;
	}

	/**
	 * Get the fee for a code
	 */
	function getFee($code) {
		$res = $this->_execute("select data from fee_schedule_data fsd inner join codes c using(code_id) where code = ".$this->_quote($code).
					" and fee_schedule_id = ".(int)$this->get('id'));
		if ($res && isset($res->fields['data'])) {
			return $res->fields['data'];
		}
		return 0;
	}

	/**
	 * Get the fee form a code_id
	 */
	function getFeeFromCodeId($code_id) {
		settype($code_id,'int');
		$res = $this->_execute("select data from fee_schedule_data fsd inner join codes c using(code_id) where c.code_id = $code_id and fee_schedule_id = ".(int)$this->get('id'));
		if ($res && isset($res->fields['data'])) {
			return $res->fields['data'];
		}
		return 0;
	}
}
?>
