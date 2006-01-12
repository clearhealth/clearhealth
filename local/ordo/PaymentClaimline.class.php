<?php
/**
 * Object Relational Persistence Mapping Class for table: payment_claimline
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: payment_claimline
 *
 * @package	com.uversainc.clearhealth
 */
class PaymentClaimline extends ORDataObject {

	/**#@+
	 * Fields of table: payment_claimline mapped to class members
	 */
	var $id			= '';
	var $payment_id		= '';
	var $code_id		= '';
	var $paid		= '';
	var $writeoff		= '';
	var $carry		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PaymentClaimline($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'payment_claimline';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Payment_claimline with this
	 */
	function setup($id = 0,$payment_id = 0) {
		if ($payment_id > 0) {
			$this->set('payment_id',$payment_id);
		}
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}
	
	/**
	 * Get datasource for paymentclaimlines from the db
	 */
	function &paymentClaimlineList($patient_id,$foreign_id = false) {
		$fwhere = "";
		if ($foreign_id !== false) {
			settype($foreign_id,'int');
			$fwhere = " and p.foreign_id = $foreign_id ";
		}
		
		$ds =& new Datasource_sql();

		$labels = array('code' => 'Code','paid' => 'Paid', 'carry' => 'Due', 'writeoff' => 'Writeoff', 'payer_name' => 'Payer');

		$ds->setup($this->_db,array(
				'cols' 	=> " paid, carry, pcl.writeoff, code, ins.name as payer_name, date_format(date_of_treatment,'%Y-%m-%d') date_of_treatment, cd.fee",
				'from' 	=> "$this->_table pcl inner join codes cds using (code_id) inner join payment p on p.payment_id=pcl.payment_id "
						. " inner join clearhealth_claim chc on chc.claim_id = p.foreign_id "
						. " inner join encounter e on e.encounter_id = chc.encounter_id "
						. " inner join coding_data cd on cd.foreign_id = e.encounter_id "
						. " inner join company ins on ins.company_id = p.payer_id ",
				'where' => " patient_id = $patient_id $fwhere",
				'groupby' => ' pcl.payment_claimline_id '
			),
			$labels
		);
		return $ds;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('payment_claimline_id');
	}

	/**#@+
	 * Getters and Setters for Table: payment_claimline
	 */

	
	/**
	 * Getter for Primary Key: payment_claimline_id
	 */
	function get_payment_claimline_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: payment_claimline_id
	 */
	function set_payment_claimline_id($id)  {
		$this->id = $id;
	}

	/**
	 * Calculate what carry would be with the current paid value
	 */
	function calculateCarry($encounterId) {
		$table = $this->tableName();
		$sql = "select 
				(cd.fee - ifnull(sum(paid),0.00)) carry 
			from 
				encounter e 
				inner join coding_data cd on cd.foreign_id = e.encounter_id
				left join clearhealth_claim chc on chc.encounter_id = e.encounter_id
				left join payment p on chc.claim_id = p.foreign_id
				left join $table pcl on p.payment_id=pcl.payment_id and pcl.code_id = ".EnforceType::int($this->get('code_id')) ."
			where
			cd.code_id = ".EnforceType::int($this->get('code_id')) ." and e.encounter_id = ".EnforceType::int($encounterId)."
			group by e.encounter_id";

		$carry = $this->dbHelper->getOne($sql);
		$this->set('carry',$carry - $this->get('paid'));
	}

	/**#@-*/
}
?>
