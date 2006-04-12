<?php
/**
 * Object Relational Persistence Mapping Class for table: claim
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */


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

	function setupByIdentifier($ident) {
		$sql = "select * from ".$this->tableName()." where identifier = ".$this->dbHelper->quote($ident);
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this,$res);
	}

	function &fromEncounterId($encounter_id) {
		settype($encounter_id,'int');

		$claim =& ORDataOBject::factory('ClearhealthClaim');

		$res = $claim->_execute("select claim_id from $claim->_table where encounter_id = $encounter_id");
		if ($res && isset($res->fields['claim_id'])) {
			$claim->setup($res->fields['claim_id']);
		}
		return $claim;

	}
	
	function accountStatus($patient_id,$encounter_id = false) {
		$status = array();
		$sql = '
			SELECT
				SUM(total_billed) AS total_billed,
				SUM(IFNULL(total_paid,0)) AS total_paid,
				SUM(IFNULL(writeoffs.writeoff,0)) AS total_writeoff,
				(SUM(IFNULL(total_billed,0)) - (SUM(IFNULL(total_paid,0)) + SUM(IFNULL(writeoffs.writeoff,0)))) AS total_balance
			FROM
				encounter AS e
				INNER JOIN clearhealth_claim AS cc USING(encounter_id)
				LEFT JOIN (
					SELECT
						foreign_id,
						SUM(ifnull(writeoff,0)) AS writeoff
					FROM
						payment 
					WHERE
						encounter_id = 0
					GROUP BY
						foreign_id
				) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)
			WHERE 
				patient_id = ' . $this->dbHelper->quote($patient_id);

		if ($encounter_id) {
			$sql .= " and e.encounter_id = ".(int)$encounter_id;
		}
		
		$res = $this->_execute($sql);
		if ($res && !$res->EOF) {
			$status['total_billed'] = $res->fields['total_billed'];
			$status['total_paid'] = $res->fields['total_paid'];
			$status['total_writeoff'] = $res->fields['total_writeoff'];
			$status['total_balance'] = $res->fields['total_balance'];
		}
		return $status;
	}

	function SummedPaymentsByCode() {
		$sql = "select codes.code, sum(pc.paid) paid, sum(pc.writeoff) writeoff, sum(pc.carry) carry from payment_claimline pc left join codes using(code_id) inner join payment p on pc.payment_id = p.payment_id where foreign_id = ".(int)$this->get('id') . " group by pc.code_id ";
		$res = $this->_execute($sql);
		$ret = array();
		while ($res && !$res->EOF) {
			$ret[$res->fields['code']] = $res->fields;
			$res->moveNext();
		}
		return $ret;
	}
	
	/**
	 * Get datasource for claims from the db
	 *
	 * @todo Move to local/datasources/ and create its own DS file
	 */
	function &claimList($patient_id,$show_lines = false,$filters = false) {
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
						$where .= " UNIX_TIMESTAMP(e.date_of_treatment) > " . $this->_quote(strtotime($fval)) . " and ";
						break;
					case 'end':
						$where .= " UNIX_TIMESTAMP(e.date_of_treatment) < " . $this->_quote(strtotime($fval)) . " and ";
						break;
					case 'facility':
						$where .= " (o.location_id = " . $this->_quote($fval) . " or e.building_id = ".$this->_quote($fval).") and ";
						break;
					case 'provider':
						$where .= " e.treating_person_id = ". (int)$fval . " and ";
						break;
					case 'payer':
						$where .= " pa.payer_id =  " . (int)$fval. " and ";
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
		$ds->setup($this->_db,array(
				'cols' 	=> '
					chc.claim_id, 
					chc.identifier,
					date_format(fbc.date_sent, "%Y-%m-%d") AS billing_date,
					date_format(e.date_of_treatment,"%Y-%m-%d") AS date_of_treatment, 
					chc.total_billed,
					chc.total_paid,
					fbco.name AS "current_payer",
					b.name facility,
					concat_ws(",",pro.last_name,pro.first_name) AS provider,
					(chc.total_billed - chc.total_paid - SUM(IFNULL(pcl.writeoff,0))) AS balance, 
					SUM(IFNULL(pcl.writeoff,0)) AS writeoff',
				'from' 	=> 
					$this->_table . ' AS chc 
					INNER JOIN encounter AS e USING(encounter_id)
					LEFT JOIN payment AS pa ON(pa.foreign_id = chc.claim_id)
					LEFT JOIN payment_claimline AS pcl ON(pcl.payment_id = pa.payment_id)
					LEFT JOIN occurences AS o ON(e.occurence_id = o.id)
					LEFT JOIN buildings AS b ON(e.building_id = b.id)
					LEFT JOIN person AS pro ON(e.treating_person_id = pro.person_id)
					LEFT JOIN fbclaim AS fbc ON(chc.identifier = fbc.claim_identifier)
					LEFT JOIN fbcompany AS fbco ON(fbc.claim_id = fbco.claim_id AND fbco.type = "FBPayer" AND fbco.index = 0)
					',
				'where' => ' e.patient_id = ' . $patient_id . $where,
				'groupby' => 'chc.claim_id'
			),
			array(
				'identifier' => 'Id',
				'billing_date' => 'Billing Date',
				'date_of_treatment' => 'Date', 
				'total_billed' => 'Billed',
				'total_paid' => 'Paid',
				'balance' => 'Balance',
				'current_payer' => 'Payer Name'
			)
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
