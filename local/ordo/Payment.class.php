<?php
/**
 * Object Relational Persistence Mapping Class for table: payment
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: payment
 *
 * @package	com.uversainc.clearhealth
 */
class Payment extends ORDataObject {

	/**#@+
	 * Fields of table: payment mapped to class members
	 */
	var $id			= '';
	var $foreign_id		= '';
	var $payment_type	= '';
	var $amount		= '';
	var $user_id		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Payment($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'payment';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Payment with this
	 */
	function setup($id = 0,$foreign_id = 0) {
		if ($foreign_id > 0) {
			$this->set('foreign_id',$foreign_id);
		}
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('payment_id');
	}
	
	/**
	 * Get datasource for payments from the db
	 */
	function paymentList($encounter_id) {
		settype($encounter_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "payment_id, foreign_id, payment_type, amount, timestamp",
				'from' 	=> "$this->_table ",
				'where' => " foreign_id = $encounter_id"
			),
			array('payment_type' => 'Type','amount' => 'Amount')
		);

		$ds->registerFilter('payment_type',array(&$this,'lookupPaymentType'));
		return $ds;
	}
	
	/**#@+
	 * Enumeration getters
	 */
	function getPaymentTypeList() {
		$list = $this->_load_enum('payment_type',false);
		return array_flip($list);
	}
	/**#@-*/

	var $_edCache = false;
	/**
	 * Cached lookup for date_type
	 */
	function lookupPaymentType($id) {
		if ($this->_edCache === false) {
			$this->_edCache = $this->getPaymentTypeList();
		}
		if (isset($this->_edCache[$id])) {
			return $this->_edCache[$id];
		}
	}
	

	/**#@+
	 * Getters and Setters for Table: payment
	 */
	
	/**
	 * Getter for Primary Key: payment_id
	 */
	function get_payment_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: payment_id
	 */
	function set_payment_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
