<?php
/**
 * Object Relational Persistence Mapping Class for table: claim
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
 * Object Relational Persistence Mapping Class for table: claim
 *
 * @package	com.uversainc.clearhealth
 */
class ClearhealthClaim extends ORDataObject {

	/**#@+
	 * Fields of table: claim mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $identifier		= '';
	var $total_billed	= '';
	var $total_paid		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function ClearhealthClaim($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'clearhealth_claim';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of ClearhealthClaim with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	function fromEncounterId($encounter_id) {
		settype($encounter_id,'int');

		$claim =& ORDataOBject::factory('ClearhealthClaim');

		$res = $claim->_execute("select claim_id from $claim->_table where encounter_id = $encounter_id");
		if ($res && isset($res->fields['claim_id'])) {
			$claim->setup($res->fields['claim_id']);
		}
		return $claim;

	}
	
	function accountStatus($patient_id) {
		$status = array();
		$sql = "SELECT sum(total_billed) as total_billed, sum(total_paid) as total_paid, (sum(total_billed) - sum(total_paid)) as total_balance "
				." FROM encounter e inner join clearhealth_claim using (encounter_id) where patient_id = " . (int)$patient_id;
		
		$res = $this->_execute($sql);
		if ($res && !$res->EOF) {
			$status['total_billed'] = $res->fields['total_billed'];
			$status['total_paid'] = $res->fields['total_paid'];
			$status['total_balance'] = $res->fields['total_balance'];
		}
		return $status;
	}
	
	/**
	 * Get datasource for claims from the db
	 */
	function claimList($patient_id,$show_lines = false,$filters = false) {
		settype($foreign_id,'int');

		$where = "";
		if (is_array($filters)) {
			foreach ($filters as $fname => $fval) {
			if  (!empty($fval)) {
				switch ($fname) {
					case 'status':
						$where .= " c.status = " . $this->_quote($fval) . " and ";
						break;
					case 'start':
						$where .= " UNIX_TIMESTAMP(c.timestamp) > " . $this->_quote(strtotime($fval)) . " and ";
						break;
					case 'end':
						$where .= " UNIX_TIMESTAMP(c.timestamp) < " . $this->_quote(strtotime($fval)) . " and ";
						break;
					case 'facility':
						$where .= " co.name = " . $this->_quote($fval) . " and ";
						break;
					case 'name':
						$where .= " (p.last_name like  " . $this->_quote("%".$fval."%") . " or p.first_name like  " . $this->_quote("%".$fval."%") . ") and ";
						break;
					case 'payer':
						$where .= " pa.name like  " . $this->_quote("%".$fval."%") . " and ";
						break;
					case 'active':
						if ($fval == 1) {
							$where .= " c.status != 'closed' and ";
						}
						break;
					case 'claim_identifier':
							$where .= " c.claim_identifier like " . $this->_quote($fval."%") . " and ";
						break;
					default:
						break;
				}	
			}
			}
		}
		$where = substr($where,0,-4);
		if (strlen($where) > 0) {
			$where = " and $where";
		}
		
		if ($foreign_id == 0) $foreign_id = "NULL";
		
		$ds =& new Datasource_sql();

		$labels = array('identifier' => 'Id','date_of_treatment' => 'Date', 'total_billed' => 'Billed','total_paid' => 'Paid', 'balance'=>'Balance');

		$ds->setup($this->_db,array(
				'cols' 	=> "chc.claim_id, chc.identifier, date_format(e.date_of_treatment,'%Y-%m-%d') date_of_treatment, chc.total_billed, chc.total_paid, "
						. " (chc.total_billed - chc.total_paid) as balance, sum(pcl.writeoff) as writeoff",
				'from' 	=> "$this->_table chc inner join encounter as e using (encounter_id) left join payment pa on pa.foreign_id = chc.claim_id left join payment_claimline as pcl on pcl.payment_id = pa.payment_id",
				'where' => " e.patient_id = $patient_id $where",
				'groupby' => " chc.claim_id "
			),
			$labels
		);
		//echo $ds->preview();
		return $ds;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('claim_id');
	}

	/**#@+
	 * Getters and Setters for Table: claim
	 */

	
	/**
	 * Getter for Primary Key: claim_id
	 */
	function get_claim_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: claim_id
	 */
	function set_claim_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
