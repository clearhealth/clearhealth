<?php
/**
 * Object Relational Persistence Mapping Class for table: payment
 *
 * @package	com.clear-health.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
$loader->requireOnce('includes/Datasource_sql.class.php');
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: payment
 *
 * @package	com.clear-health.clearhealth
 */
class Payment extends ORDataObject {

	/**#@+
	 * Fields of table: payment mapped to class members
	 */
	var $payment_id		= '';
	
	/**
	 * An integer referencing the id of a {@link ClearhealthClaim}
	 */
	var $foreign_id		= '';
	var $encounter_id	= '';
	var $payment_type	= '';
	var $ref_num		= '';
	var $amount		= '';
	var $writeoff		= '';
	var $user_id		= '';
	var $payer_id		= '';
	var $payment_date	= '';
	var $title		= '';
	/**#@-*/


	var $_table = 'payment';
	var $_sequence_name = 'sequences';	
	var $_key = 'payment_id';
	var $_internalName='Payment';

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
	 * make sure that user_id gets set
	 * @todo: move to getter
	 */
	function persist() {
		$u = $this->get('user_id');
		if ( empty($u) ) {
			$me =& Me::getInstance();
			$this->set('user_id',$me->get_user_id());
		}
		parent::persist();
	}

	/**
	 * Returns an array of {@link Payment}s based on the ID of a parent claim.
	 *
	 * @param  int  A {@link ClearhealthClaim} ID
	 * @return array
	 *
	 * @todo: move to an ordo collection
	 */
	function &fromForeignId($id) {
		settype($id,'int');

		$p =& Celini::newOrdo('Payment');
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
	 * @todo: move Datasource to its own class
	 */
	function &paymentList($foreign_id,$extraCols = false) {
		settype($foreign_id,'int');
		if ($foreign_id == 0) $foreign_id = "NULL";
		
		$ds =& new Datasource_sql();

		$labels = array('payment_type' => 'Type','payment_date' => 'Payment Date', 'amount' => 'Amount');
		if ($extraCols) {
			$labels['writeoff'] = "Write Off";
			$labels['payer_id'] = "Payer";
			$ds->registerFilter('payer_id',array(&$this,'lookupPayer'));
		}
	       	$labels['title'] = 'Title';
	       	$labels['ref_num'] = 'Chk #';

		$format = DateObject::getFormat();

		$ds->setup($this->_db,array(
				'cols' 	=> "
					payment_id, 
					foreign_id, 
					payment_type, 
					amount, 
					writeoff, 
					payer_id, 
					DATE_FORMAT(payment_date, '$format') payment_date, 
					timestamp, 
					title, 
					ref_num
				",
				'from' 	=> $this->tableName(),
				'where' => " foreign_id = $foreign_id"
			),
			$labels
		);
		$ds->registerFilter('payment_type',array(&$this,'lookupPaymentType'));
		return $ds;
	}
	
	/**
	 * Get datasource for payments from the db for a specific encounter
	 * @todo: move Datasource to its own class
	 */
	function &paymentsFromEncounterId($foreign_id,$extraCols = false) {
		settype($foreign_id,'int');
		if ($foreign_id == 0) $foreign_id ="NULL";
		
		$ds =& new Datasource_sql();
		$labels = array('payment_type' => 'Type','payment_date' => 'Payment Date', 'amount' => 'Amount','title' => 'Title','ref_num' => 'Chk #');
		if ($extraCols) {
			$labels['writeoff'] = "Write Off";
			$labels['payer_id'] = "Payer";
			$ds->registerFilter('payer_id',array(&$this,'lookupPayer'));
		}

		$ds->setup($this->_db,array(
				'cols' 	=> "
					payment_id, 
					foreign_id, 
					encounter_id,
					payment_type, 
					amount, 
					writeoff, 
					payer_id, 
					DATE_FORMAT(payment_date, '".DateObject::getFormat()."') payment_date, 
					timestamp, 
					title, 
					ref_num
				",
				'from' 	=> $this->tableName(),
				'where' => " encounter_id = $foreign_id"
			),
			$labels
		);
		$ds->registerFilter('payment_type',array(&$this,'lookupPaymentType'));
		return $ds;
	}
	
	
	/**#@+
	 * Enumeration getters
	 * @todo: Upgrade to new enum code
	 */
	function getPaymentTypeList() {
		$list = $this->_load_enum('payment_type',false);
		return array_flip($list);
	}
	/**#@-*/

	var $_edCache = false;
	/**
	 * Cached lookup for date_type
	 * @todo: Upgrade to new enum code
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
	/**
	 * @todo: Upgrade to new enum code
	 */
	function lookupPayer($id) {
		if ($this->_pCache === false) {
			$insuranceProgram =& ORDataObject::Factory('InsuranceProgram');
			$this->_pCache = $insuranceProgram->ProgramList();
		}
		if (isset($this->_pCache[$id])) {
			return $this->_pCache[$id];
		}
	}
	

	/**#@+
	 * Getters and Setters for Table: payment
	 */
	
	function set_payment_date($date) {
		$this->_setDate('payment_date', $date);
	}

	function get_payment_date() {
		if (empty($this->payment_date) || $this->payment_date->isEmpty()) {
			$this->set('payment_date', date('m/d/Y'));
		}
		return $this->_getDate('payment_date');
	}

	function get_payment_type() {
		if ($this->payment_type == '') {
			// default to check
			$em =& Celini::enumManagerInstance();
			$this->payment_type = $em->lookupKey('payment_type','check');
		}
		return $this->payment_type;
	}

	/**#@-*/

	function totalPaidForCodeId($code_id) {
		$res = $this->_execute("select sum(pc.paid) p from payment_claimline pc inner join $this->_table using(payment_id) where foreign_id = ".(int)$this->get('foreign_id')." and code_id = $code_id");
		if ($res && isset($res->fields['p'])) {
			return $res->fields['p'];
		}
		return 0;
	}
	function totalPaidForCodingDataId($coding_data_id) {
		$res = $this->_execute("select sum(pc.paid) p from payment_claimline pc inner join $this->_table using(payment_id) where foreign_id = ".(int)$this->get('foreign_id')." and coding_data_id = $coding_data_id");
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
	function totalWriteoffForCodingDataId($coding_data_id) {
		$res = $this->_execute("select sum(pc.writeoff) w from payment_claimline pc inner join $this->_table using(payment_id) where foreign_id = ".(int)$this->get('foreign_id')." and coding_data_id = $coding_data_id");
		if ($res && isset($res->fields['w'])) {
			return $res->fields['w'];
		}
		return 0;
	}
}
?>
