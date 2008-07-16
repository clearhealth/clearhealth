<?php
/**
 * Object Relational Persistence Mapping Class for table: claim
 *
 * @package	com.clear-health.clearhealth
 * @subpackage	billing
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
 $loader->requireOnce('/includes/Datasource_sql.class.php');
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: claim
 *
 * @package	com.clear-health.freeb2
 */
class FBClaim extends ORDataObject {

	/**#@+
	 * Fields of table: claim mapped to class members
	 */
	var $id			= '';
	var $claim_identifier	= '';
	var $revision		= 1;
	var $status		= 'new';
	var $format		= '';
	var $date_sent		= '';
	var $timestamp		=  '';
	var $audit_number 	= 0;
	var $comment 		= '';
	var $claim_mode 	= "P";
	var $amount_total = '';
	var $amount_paid = '';
	var $net_amount_total = '';
	/**#@-*/

	var $_key = 'claim_id';
	
	/**
	 * Metadata for storage variables
	 *
	 * format is
	 *
	 * [type][key] = key
	 */
	var $storage_metadata = array(
		'int' => array(), 
		'date' => array(),
		'string' => array(
			"hcfa_10d_comment" => "",
			"auto_accident_state" => "",
			"medicaid_resubmission_code" => "",
			"original_reference_number" =>"", 
			"prior_authorization_number" => "",
			'claim_type' => 'medical',
			"attachment_control_number" => ""
		)
	);

	/**#@+
	 * {@inheritdoc}
	 */
	var $_table = 'fbclaim';
	/**#@-*/

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FBClaim($db = null) {
		parent::ORDataObject($db);
		$this->_sequence_name = 'sequences';
		$this->enumTable = $this->_table;
		$em =& Celini::enumManagerInstance();
		$config =& Celini::configInstance('Practice');
		
		if(isset($config->_corral['BillingType'])){
			$key = $config->_corral['BillingType'];
			$this->claim_mode = $em->lookup('billing_mode', $key, 'extra1');
		}
		else {
			$this->claim_mode = "P";	
		}
		
		
		//$this->addMetaHints('hide', array('claim_type'));
		//$this->addMetaHints("hide",array("claim_id","claim_identifier","revision","open"));
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Claim with this
	 */
	function setup($id = 0) {
		$this->set('id',$id);
		$this->set("timestamp",date("Y-m-d"));

		if ($id > 0) {
			$this->populate();
		}
		$this->audit_number = $this->get("id") . "-" . $this->get("revision") . "-" . time();
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('claim_id');
	}
	
	/**
	 * Get the next revision for this claim identifier 
	 */
	function nextRevision() {
		return $this->_lastRevision() + 1;
	}
	
	/**
	 * Returns the last revision from of this claim
	 *
	 * @return int
	 * @access private
	 */
	function _lastRevision() {
		$sql = sprintf(
			'SELECT revision FROM fbclaim WHERE claim_identifier = %s ORDER BY revision DESC LIMIT 1',
			$this->_quote($this->get("claim_identifier")));
		
		$result = $this->_execute($sql);
		if($result && !$result->EOF) {
			return $result->fields["revision"];
		}
		else {
			return 0;
		}
	}
	
	/**
	 * Populate the class from the db
	 */
	function persist($revise = false) {
		 if ($revise)  {
			$this->set("revision",$this->nextRevision());
		}
		$sql = "REPLACE INTO fblatest_revision set claim_identifier=" . $this->_quote($this->get("claim_identifier")) . ", revision= " . $this->_quote($this->get("revision"));
		$result = $this->_execute($sql);
		parent::persist();
	}

	/**
	 * Return a datsource containing infomration about all the lines for this claim
	 */
	function &lineList() {
		$cl =& ORDataObject::factory('FBClaimline');
		return $cl->lineList($this->get('id'));
	}

	/**
	 * Return a child entity for this claim
	 * if id is 0 a new entity of that type will be returned
	 *
	 * @param	string	$entityType
	 * @param	int	$id
	 *
	 * @todo Determine if possible, and if so, remove $claim_id.  It doesn't appears to be in use
	 *       any longer.
	 */
	function &childEntity($entityType,$claim_id = 0,$index = 0) {
		$return =& Celini::newORDO($entityType, array(0, $this->get('id'), $index));
		
		// hide teeth fields if this isn't a dental claim
		if ($entityType == 'FBClaimline' && $this->get('claim_type') != 'dental') {
			$return->addMetaHints('hide', array('tooth', 'toothside'));
		}
		
		return $return;
	}
	
	/**
	 * Return child enities in an array for this claim
	 *
	 * @param	string	$entityType
	 * @param	int	$id
	 */
	function &childEntities($entityType = "", $claim_id = 0) {
		//return all entities
		if (empty($entityType)) {
			$entities = array();
			$sql = "SELECT DISTINCT type from fbperson where claim_id = " .  $this->_quote($claim_id) . " UNION select distinct type from fbcompany where claim_id = " .  $this->get("claim_id");
			$result = $this->_execute($sql);
			//echo $sql;
			while ($result && !$result->EOF) {
				$ce =& ORDataObject::factory($result->fields['type'], $claim_id, $this->get("id"));
				$entities = array_merge($entities,$ce->arrayFromClaimId($this->get("claim_id")));	
				$result->MoveNext();	
			}
			
			$ce =& ORDataObject::factory("FBClaimline", $claim_id, $this->get("id"));
			$entities = array_merge($entities,$ce->arrayFromClaimId($this->get("claim_id")));
			return $entities;
		}
		//return entities of a particular type
		$ce =& ORDataObject::factory($entityType, $claim_id, $this->get("claim_id"));
		$return =& $ce->arrayFromClaimId($this->get('claim_id'));
		
		// hide teeth fields if this isn't a dental claim
		if ($entityType == 'FBClaimline' && $this->get('claim_type') != 'dental') {
			for ($i = 0; $i < count($return); $i++) {
				$return[$i]->addMetaHints('hide', array('tooth', 'toothside'));
			}
		}
		
		return $return;
	}
	
	/**
	 * Get all people associated with this claim
	 */
	function &personList() {
		$p =& ORDataObject::factory('Person');
		return $p->getPersonList($this->get('id'));
	}
	
	/**
	 * Get all companies associated with this claim
	 */
	function &companyList() {
		$c =& ORDataObject::factory('Company');
		return $c->getCompanyList($this->get('id'));
	}
	
	/**
	 * Get latest rev of all claims in the db for use in a grid
	 */
	function &claimList($filters = false) {
		$where = " status != 'deleted' and ";
		$having = "";
		if (is_array($filters)) {
			foreach ($filters as $fname => $fval) {
				if  (!empty($fval)) {
				switch ($fname) {
					case 'status':
						$where .= " c.status = " . str_replace("'","",$this->_quote($fval)) . " and ";
						break;
					case 'start':
						$where .= " c.timestamp >= " . $this->_quote($fval.' 00:00:00') . " and ";
						break;
					case 'end':
						$where .= " c.timestamp <= " . $this->_quote($fval.' 23:59:59') . " and ";
						break;
					case 'facility':
						$where .= " co.name = " . $this->_quote($fval) . " and ";
						break;
					case 'name':
						$qProviderLastName = $this->dbHelper->quote('%' . $filters['patient_lastName'] . '%');
						$qProviderFirstName = $this->dbHelper->quote('%' . $filters['patient_firstName'] . '%');
						$where .= " ( p.last_name LIKE {$qProviderLastName} OR  p.first_name LIKE {$qProviderFirstName} ) AND ";
						break;
					case 'payer':
						$where .= " pa.name like  " . $this->_quote("%".$fval."%") . " and ";
						break;
					case 'claim_identifier':
							$where .= ' (c.claim_identifier like ' . $this->_quote($fval.'%') . ' OR c.claim_id like ' . $this->dbHelper->quote($fval . '%') . ') and ';
						break;
					case 'dos_start':
							$having .= " min(cl.date_of_treatment) >= " . $this->_quote($fval.' 00:00:00') . " and ";
						break;
					case 'dos_end':
							$having .= " min(cl.date_of_treatment) <= " . $this->_quote($fval.' 23:59:59') . " and ";
						break;
					case 'provider':
						$qProviderLastName = $this->dbHelper->quote('%' . $filters['provider_lastName'] . '%');
						$qProviderFirstName = $this->dbHelper->quote('%' . $filters['provider_firstName'] . '%');
						$where .= " ( pro.last_name LIKE {$qProviderLastName} OR  pro.first_name LIKE {$qProviderFirstName} ) AND ";
						break;
					case 'id':
							$where .= " c.claim_id in (".implode(',',$fval).") and ";
						break;
					case 'program':
						$where .= " pa.program_name  like " . $this->_quote("%".$fval."%") . " and ";
						break;

					default:
						break;
				}	
				}
			}
		}
		$where = substr($where,0,-4);
		$having = substr($having,0,-4);
		if (!empty($having)) {
			$having = " having $having";
		}
		
		$userProfile =& Celini::getCurrentUserProfile();
		$qCurrentPracticeId = $this->dbHelper->quote($userProfile->getCurrentPracticeId());
		// "OR prac_id.value IS NULL" is here for backward compatibility so all of the
		// old claims to not disappear.
		$where .= " AND (prac_id.value = {$qCurrentPracticeId} OR prac_id.value IS NULL OR prac_id.value = '')";
		
		$sql = array(
					 'cols'  => "
					 	c.claim_id, 
						c.claim_identifier, 
						c.revision, 
						c.status,
						pa.program_name,
		 				case when c.date_sent != '0000-00-00 00:00:00' then DATE_FORMAT(c.date_sent,'%m/%d/%Y') else DATE_FORMAT(c.timestamp,'%m/%d/%Y') end as timestamp,
						sum(cl.amount) as amount, 
						concat_ws(', ',p.last_name,p.first_name) as patient_name,  
						p.record_number, 
					 	p.identifier as patient_identifier, 
						co.name as facility_name, 
						pa.name as payer_name, 
						date_format(min(cl.date_of_treatment),'%m/%d/%Y') date_of_treatment, 
						c.claim_identifier `delete`, 
						concat_ws(', ',pro.last_name,pro.first_name) as provider_name,
						concat_ws(', ',sub.last_name,sub.first_name) as subscriber_name,
						concat_ws(', ',refpro.last_name,refpro.first_name) as referring_provider_name,
						concat_ws(', ',suppro.last_name,suppro.first_name) as supervising_provider_name,
						concat_ws(', ',resp.last_name,resp.first_name) as responsible_party_name,
						concat_ws(', ',bc.name,bc.phone_number) as billing_contact,
						concat_ws(', ',ch.name,ch.phone_number) as clearing_house,
						concat_ws(', ',bf.name,bf.phone_number) as billing_facility
						",
					 'from'  => "
					 	fblatest_revision lr 
						left join $this->_table c using (claim_identifier,revision) left join fbclaimline cl using(claim_id) 
					  	left join fbperson as p on p.claim_id = c.claim_id and p.type='FBPatient' and p.`index` = 0
					  	left join fbcompany as co on p.claim_id = co.claim_id and co.type='FBTreatingFacility' and co.`index` = 0
					  	left join fbcompany as pa on p.claim_id = pa.claim_id and pa.type='FBPayer' and pa.`index` = 0
						left join storage_string as ss on ss.foreign_key = pa.company_id and ss.value_key = 'program_name'
					  	left join fbperson as pro on pro.claim_id = c.claim_id and pro.type='FBProvider' and pro.`index` = 0
					  	left join fbperson as sub on sub.claim_id = c.claim_id and sub.type='FBSubscriber' and sub.`index` = 0
					  	left join fbperson as refpro on refpro.claim_id = c.claim_id and refpro.type='FBReferringProvider' and refpro.`index` = 0
					  	left join fbperson as suppro on suppro.claim_id = c.claim_id and suppro.type='FBSupervisingProvider' and suppro.`index` = 0
					  	left join fbperson as resp on resp.claim_id = c.claim_id and resp.type='FBResponsibleParty' and resp.`index` = 0
					  	left join fbcompany as bc on bc.claim_id = c.claim_id and bc.type='FBBillingContact' and bc.`index` = 0
					  	left join fbcompany as ch on ch.claim_id = c.claim_id and ch.type='FBClearingHouse' and ch.`index` = 0
					  	left join fbcompany as bf on bf.claim_id = c.claim_id and bf.type='FBBillingFacility' and bf.`index` = 0
						LEFT JOIN fbcompany AS prac ON (prac.claim_id = c.claim_id AND prac.type='FBPractice' AND prac.`index` = 0)
						LEFT JOIN storage_int AS prac_id ON (prac_id.foreign_key = prac.company_id AND prac_id.value_key = 'practice_id')
						",
					 'groupby' => 'c.claim_id '.$having,
					 'where' => $where, 
					);
		$cols = array(	"batch"=>'</a><input style="margin-left:12px;" type="checkbox" name="checkall" onClick="checkAll(this)">', 
			/*'claim_identifier' => 'Identifier',*/
			'claim_id' => 'Trns',
			"date_of_treatment" => 'DOS',
			'revision'=>'R', 
			/*'status' => 'Status', */
			'patient_name'=> 'Name', 
			'record_number' => 'Rec#', 
			"facility_name" => "Facility", 
			"provider_name" => "Provider",
			"program_name" => "Program",
			'payer_name' => 'Payer',  
			'amount'=>'Amount', 
			"timestamp" => "Rev Date",
		);
		
		$ds =& new Datasource_sql();
		$ds->addDefaultOrderRule('claim_id', 'DESC');

		$ds->setup($this->_db,$sql,$cols);
		$ds->orderHints['batch'] = 'c.claim_id';
		$ds->registerFilter('payer_name',array(&$this,'_paymentLink'));

		$ds->registerFilter('timestamp',array(&$this,'_status'));
		return $ds;
	}

	function _status($id,$row) {
		return "<span class='status $row[status]'>$id</span>";
	}
	
	function _paymentLink($payer_name,$row) {
		$string = $payer_name;
		if ($row['status'] == "sent") {
			$encounter_id = preg_replace("/-.*$/","",$row['claim_identifier']);
			$string .= ' <a href="' . Celini::link('payment','Eob',"main",$encounter_id) . '">EOB</a>';
		}
		return $string;
	}
		
	/**
	 * Get all revisions for a particular claim in the db for use in a grid
	 */
	function &claimRevisionList($entity_id = false, $entity_type = false) {
		$tableName = $this->tableName();
		$qEntityId = $this->dbHelper->quote($entity_id);
		$sql = array(
			'cols'  => "
				c.claim_id,
				c.claim_identifier,
				c.revision,
				c.status,
				DATE_FORMAT(c.timestamp,'%m/%d/%Y') AS timestamp,
				SUM(cl.amount) AS amount,
				CONCAT_WS(', ',p.last_name,p.first_name) AS patient_name,
				p.record_number,
				p.identifier AS patient_identifier,
				co.name AS facility_name,
				pa.name AS payer_name ",
			'from'  => " 
				{$tableName} AS c
				LEFT JOIN fbclaimline AS cl USING(claim_id) 
				LEFT JOIN fbperson as p ON(p.claim_id = c.claim_id AND p.type='FBPatient' AND p.`index` = 0)
				LEFT JOIN fbcompany AS co ON(p.claim_id = co.claim_id AND co.type='FBTreatingFacility' AND co.`index` = 0)
				LEFT JOIN fbcompany AS pa ON(p.claim_id = pa.claim_id AND pa.type='FBPayer' AND pa.`index` = 0)",
			'where' => "c.claim_identifier = {$qEntityId}",
			'groupby' => 'c.claim_id'
		);
		$labels = array(
			'revision'=>'Rev',
			'status' => 'Status',
			'claim_id' => 'Trns',
			'patient_name'=> 'Name',
			'record_number' => 'Rec #',
			'facility_name' => 'Facility',
			'timestamp' => 'Date',
			'payer_name' => 'Payer',
			'amount'=>'Amount'
		);					 

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,$sql,$labels);
		$ds->addDefaultOrderRule('revision', 'ASC');
		return $ds;
	}
	
	/**
	 * Get a claim object from the claim identifier handle revisions, if no
	 * revision specified open revision of claim
	 * 
	 */
	function &fromClaimId($claim_identifier,$revision = 0) {
		$c =& ORDataObject::factory("FBClaim");
		$c->set("claim_identifier",$claim_identifier);
		if (strlen($c->get("claim_identifier")) > 0) {
			$sql = "SELECT claim_id from fbclaim where claim_identifier = " . $c->_quote($claim_identifier);
			if ($revision > 0) {
				$sql .= " and revision = " . $c->_quote($revision);
			}
			else {
				$sql .= " order by revision DESC limit 1";	
			}
			$result = $c->_execute($sql);
			$id = 0;
			if ($result && !$result->EOF) {
				$id = $result->fields['claim_id'];
			}
			$c->setup($id);
		}
		
		return $c;
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
	
	/**
	 * Getter for Total of amounts paid on all claim lines: amount_paid
	 */
	function get_amount_paid() {
		$cls = $this->childEntities("FBClaimline");
		$amount = 0.00;
		foreach($cls as $cl) {
			$amount += $cl->get("amount_paid");	
		}
		return $amount;
	}
	
	function value_amount_paid() {
		$amountPaid = $this->get('amount_paid');
		return $amountPaid > 0 ? round($this->get('amount_paid'), 2) : '0.00';
	}
	
	/**
	 * An alias for BC.  Use <i>$claim->value('net_amount_total')</i>
	 *
	 * @deprecated
	 * @see value_net_amount_total()
	 */
	function get_net_amount_total() {
		return $this->value('net_amount_total');
	}
	
	/**
	 * Getter for Total amount due minus total amount paid on all claim lines:
	 * net_amount_total
	 */
	function value_net_amount_total() {
		$cls = $this->childEntities("FBClaimline");
		$amount_paid = 0.00;
		$amount_total = 0.00;
		foreach($cls as $cl) {
			$amount_paid += $cl->get("amount_paid");
			$amount_total += $cl->get("amount");	
		}
		$net_amount_total = $amount_total - $amount_paid;
		if ($net_amount_total < 0) {
			$net_amount_total = 0.00;
		}
		
		return sprintf("%01.2f",$net_amount_total);
	}
	
	/**
	 * Getter for Total amount due amount_total
	 */
	function get_amount_total() {
		$cls = $this->childEntities("FBClaimline");
		$amount_total = 0.00;
		foreach($cls as $cl) {
			$amount_total += $cl->get("amount");	
		}
		
		if ($amount_total < 0) {
			$amount_total = 0.00;
		}
		
		return sprintf("%01.2f",$amount_total);
	}
	
	/**
	 * Getter for Total amount due amount_total
	 */
	function get_amount_paid_total() {
		$cls = $this->childEntities("FBClaimline");
		$amount_total = 0.00;
		foreach($cls as $cl) {
			$amount_total += $cl->get("amount_paid");	
		}
		
		if ($amount_total < 0) {
			$amount_total = 0.00;
		}
		
		return sprintf("%01.2f",$amount_total);
	}
	
	
	/**
	 * Return all of the diagnosis codes for all of the claim lines for this claim.
	 *
	 * @return array
	 * @access protected
	 */
	function value_allDiagnoses() {
		$diagnosesArray = array();
		$claimLines = $this->childEntities('FBClaimline');
		foreach(array_keys($claimLines) as $claimLineKey) {
			$claimLineDiagnoses = $claimLines[$claimLineKey]->value('all_diagnoses');
			foreach($claimLineDiagnoses as $claimLineDiagnosis) {
				if (!in_array($claimLineDiagnosis, $diagnosesArray)) {
					$diagnosesArray[] = $claimLineDiagnosis;
				}
			}
		}
		
		return $diagnosesArray;
	}
	
	function get_lab_amount() {
		//return "0.00";
		return "";
		// this should print blank until lab amount is implemented.	
	}
	
	/**
	 * Sets the date this claim was sent, insuring it's ISO formatted
	 */
	function set_date_sent($date) {
		$this->_setDate('date_sent', $date);
	}
	
	/**
	 * Returns the date this claim was sent, using the configured formatting
	 *
	 * @return string
	 */
	function get_date_sent() {
		return $this->_getDate('date_sent');
	}


	function is($field_name,$char_to_return = "") {
	$field_name = strtolower($field_name);
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

		switch($field_name){


			case "employment_related":
				if (strtolower($this->get("employment_related"))=="yes"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;	
			default:
					if($rc){
						return " ";
					}else{
						return(false);
					}
		}

	}
	
	function is_not($field_name, $char_to_return = "") {
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

		if($this->is($field_name)){
			if($rc){
				return " ";
			}else{
				return(false);
			}
		}
		else{
			if($rc){
				return $char_to_return;
			}else{
				return(true);
			}
			
		}	
	}


	/**#@-*/
	
	function statusList() {
		$list = $this->_load_enum('status',false);
		$list = array_merge(array(" " =>  ""), $list);
		return array_flip($list);	
	}
	
	function valueList_status() {
		return $this->statusList();
	}

	function statusListWithColor() {
		$colors = array('#c6ffbe','#fffbac','#d8f2f3','#d8dbf3','#d79999');
		$sl = $this->statusList();
		unset($sl['']);

		$i = 0;
		$ret = array();
		foreach($sl as $key => $val) {
			$ret[$key] = array('val'=>$val,'color'=>$colors[$i++]);
		}
		return $ret;
	}
	
	function facilityList() {
		$array = array("" => " ");
		$sql = "SELECT DISTINCT name from fbcompany co inner join fbclaim clm using(claim_id) where co.type = 'FBTreatingFacility'";
		$result = $this->_execute($sql);
		while ($result && !$result->EOF) {
			$array[$result->fields['name']] = $result->fields['name'];
			$result->MoveNext();
		}
		
		return $array;
	}	
	
	/**
	 * Is this the latest revision of this particular claim?
	 *
	 * @return boolean
	 */
	function isLatest() {
		return ($this->_lastRevision() == $this->get('revision')); 
	}
	
	/**
	 * Should this claim's revision number advanced if it were to be opened?
	 *
	 * In order to advance to the next revision, the claim must have been
	 * persisted, and must either not have a status of "new" or have a status 
	 * of "new" and not be the latest revision.
	 *
	 * @return boolean
	 */
	function shouldCreateNewRevision() {
		if ($this->get('claim_id') > 0 && 
			(($this->get('status') != 'new') || ($this->get('status') == 'new' && !$this->isLatest()))) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>
