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
require_once CELLINI_ROOT."/includes/Datasource_sql.class.php";
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
	var $encounter_id	= '';
	var $payment_type	= '';
	var $amount		= '';
	var $writeoff		= '';
	var $user_id		= '';
	var $payer_id		= '';
	var $payment_date	= '';
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
	
	function &fromForeignId($id) {
		settype($id,'int');

		$p =& ORDAtaObject::Factory('Payment');
		$res = $p->_execute("select * from $p->_table where foreign_id = $id order by timestamp");

		$ret = array();
		$i = 0;
		while($res && !$res->EOF) {
			$ret[$i] =& new Payment();
			$ret[$i]->populate_array($res->fields);
			$res->MoveNext();
			$i++;
		}
		return $ret;
	}
	
	/**
	 * Get datasource for payments from the db
	 */
	function paymentList($foreign_id,$extraCols = false) {
		settype($foreign_id,'int');
		if ($foreign_id == 0) $foreign_id = "NULL";
		
		$ds =& new Datasource_sql();

		$labels = array('payment_type' => 'Type','payment_date' => 'Payment Date', 'amount' => 'Amount');
		if ($extraCols) {
			$labels['writeoff'] = "Write Off";
			$labels['payer_id'] = "Payer";
			$ds->registerFilter('payer_id',array(&$this,'lookupPayer'));
		}

		$ds->setup($this->_db,array(
				'cols' 	=> "payment_id, foreign_id, payment_type, amount, writeoff, payer_id, payment_date, timestamp",
				'from' 	=> "$this->_table ",
				'where' => " foreign_id = $foreign_id"
			),
			$labels
		);
		$ds->registerFilter('payment_type',array(&$this,'lookupPaymentType'));
		return $ds;
	}
	
	/**
	 * Get datasource for payments from the db for a specific encounter
	 */
	function paymentsFromEncounterId($foreign_id,$extraCols = false) {
		settype($foreign_id,'int');
		if ($foreign_id == 0) $foreign_id ="NULL";
		
		$ds =& new Datasource_sql();

		$labels = array('payment_type' => 'Type','payment_date' => 'Payment Date', 'amount' => 'Amount');
		if ($extraCols) {
			$labels['writeoff'] = "Write Off";
			$labels['payer_id'] = "Payer";
			$ds->registerFilter('payer_id',array(&$this,'lookupPayer'));
		}

		$ds->setup($this->_db,array(
				'cols' 	=> "payment_id, foreign_id, encounter_id, payment_type, amount, writeoff, payer_id, payment_date, timestamp",
				'from' 	=> "$this->_table ",
				'where' => " encounter_id = $foreign_id"
			),
			$labels
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

	var $_pCache = false;
	function lookupPayer($id) {
		if ($this->_pCache === false) {
			$company =& ORDataObject::Factory('Company');
			$ds = $company->companyListForType('Insurance');
			$this->_pCache = $ds->toArray('company_id','name');
		}
		if (isset($this->_pCache[$id])) {
			return $this->_pCache[$id];
		}
		else if ($id > 0) {
			return "Self Pay";
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

	function set_payment_date($date) {
		$this->payment_date = $this->_mysqlDate($date);
	}

	function get_payment_date() {
		if (empty($this->payment_date)) {
			$this->payment_date = date('Y-m-d');
		}
		return $this->payment_date;
	}

	/**#@-*/

	function totalPaidForCodeId($code_id) {
		$res = $this->_execute("select sum(pc.paid) p from payment_claimline pc inner join $this->_table using(payment_id) where foreign_id = ".(int)$this->get('foreign_id')." and code_id = $code_id");
		if ($res && isset($res->fields['p'])) {
			return $res->fields['p'];
		}
		return 0;
	}
	function totalWriteoffForCodeId($code_id) {
		$res = $this->_execute("select sum(pc.writeoff) w from payment_claimline pc inner join $this->_table using(payment_id) where foreign_id = ".(int)$this->get('foreign_id')." and code_id = $code_id");
		if ($res && isset($res->fields['w'])) {
			return $res->fields['w'];
		}
		return 0;
	}
}
?>
