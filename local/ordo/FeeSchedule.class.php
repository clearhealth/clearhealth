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
$loader->requireOnce('includes/Datasource_sql.class.php');
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
	var $priority = "";
	/**#@-*/
	var $_table = 'fee_schedule';
	var $_internalName='FeeSchedule';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FeeSchedule($db = null) {
		parent::ORDataObject($db);
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
	function &listFeeSchedules() {
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
	 * Return an array suitable for populating a drop down
	 */
	function toArray() {
		$ar = $this->listFeeSchedules();
		return $ar->toArray("fee_schedule_id","label");
		
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('fee_schedule_id');
	}
	
	/**
	 * Persist the class from to db
	 */
	function persist() {
		//make this the default fee schedule with priority 1 if there isn't already a default fee schedule'
		if ($this->get("fee_schedule_id") < 2 && $this->get("priority") == "") {
			$sql = "SELECT COUNT(*) as count from $this->_table where priority = 1";
			$result = $this->_execute($sql);
			if ($result && !$result->EOF && $result->fields['count'] == 0) {
				$this->set("priority",1);
			}	
		}
		if ($this->get("priority") == "") {
			$this->set("priority",2);	
		}
		return parent::persist();
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
		//priority of one is default
		$res = $feeSchedule->_execute("select fee_schedule_id from $feeSchedule->_table where priority = 1 limit 1");
		if ($res && isset($res->fields['fee_schedule_id'])) {
			$feeSchedule->setup($res->fields['fee_schedule_id']);
		}
		return $feeSchedule;
	}
	
	/**
	 * Get the fee for a code
	 */
	function getFee($code,$modifier=-1) {

		$fsdId = Enforcetype::int($this->get('id'));
		$code = $this->dbHelper->quote($code);
		$mod = Enforcetype::int($modifier);
					
		$sql = "select 
				case when (fsdm.fee is not null) then
					fsdm.fee
				else
					case when (fsd.data is not null) then 
						fsd.data 
					else 
						fsdd.data 
					end
				end as data 
			from 
				codes
				NATURAL JOIN 
				fee_schedule fs 
				left join fee_schedule_data fsdd on (codes.code_id = fsdd.code_id and fsdd.fee_schedule_id = fs.fee_schedule_id) 
				left join fee_schedule_data fsd on (codes.code_id = fsd.code_id and fsd.fee_schedule_id = $fsdId) 
				left join fee_schedule_data_modifier fsdm on 
					(codes.code_id = fsdm.code_id and fsdm.fee_schedule_id = $fsdId and fsdm.modifier = $mod) 
			where 
				fs.priority = 1 and (fsdd.code_id IS NOT NULL or fsd.code_id IS NOT NULL) and codes.code =  $code
			GROUP BY code
			order by 
				code
			";
				
		$res = $this->dbHelper->execute($sql);
		if ($res && isset($res->fields['data'])) {
			return $res->fields['data'];
		}
		
		return 0.00;
	}

	/**
	 * Get the fee form a code_id
	 */
	function getFeeFromCodeId($code_id) {
		settype($code_id,'int');
		
		$sql = "select "
				." case when (fsd.data > 0) then fsd.data else fsdd.data end as data "
				." from codes NATURAL JOIN fee_schedule fs "
				." left join fee_schedule_data fsdd on (codes.code_id = fsdd.code_id and fsdd.fee_schedule_id = fs.fee_schedule_id) "
				." left join fee_schedule_data fsd on (codes.code_id = fsd.code_id and fsd.fee_schedule_id = " . (int)$this->get('id') . ") " 
				." where fs.priority = 1 and (fsdd.code_id IS NOT NULL or fsd.code_id IS NOT NULL) and codes.code_id =  $code_id "
				." group by code "
				." order by code";
		$res = $this->_execute($sql);
		if ($res && isset($res->fields['data'])) {
			return $res->fields['data'];
		}
		return 0.00;
	}

	function getMappedCodeFromCodeId($code_id) {
		settype($code_id,'int');
		
		$sql = "select "
				." case when (fsd.mapped_code > 0) then fsd.mapped_code else fsdd.mapped_code end as mapped_code "
				." from codes NATURAL JOIN fee_schedule fs "
				." left join fee_schedule_data fsdd on (codes.code_id = fsdd.code_id and fsdd.fee_schedule_id = fs.fee_schedule_id) "
				." left join fee_schedule_data fsd on (codes.code_id = fsd.code_id and fsd.fee_schedule_id = " . (int)$this->get('id') . ") " 
				." where fs.fee_schedule_id = ".$this->get('id')." and codes.code_id =  $code_id "
				." order by code";
		
		$res = $this->_execute($sql);
		if ($res && isset($res->fields['mapped_code'])) {
			return $res->fields['mapped_code'];
		}
		return 0.00;
	}

	function getMappedCodeFromCodingDataId($coding_data_id) {
		settype($code_id,'int');
		
		$sql = "select "
				." case when (fsd.mapped_code > 0) then fsd.mapped_code else fsdd.mapped_code end as mapped_code "
				." from coding_data left join codes using(code_id) NATURAL JOIN fee_schedule fs "
				." left join fee_schedule_data fsdd on (codes.code_id = fsdd.code_id and fsdd.fee_schedule_id = fs.fee_schedule_id) "
				." left join fee_schedule_data fsd on (codes.code_id = fsd.code_id and fsd.fee_schedule_id = " . (int)$this->get('id') . ") " 
				." where fs.fee_schedule_id = ".$this->get('id')." and codes.code_id =  $code_id "
				." order by code";
		
		$res = $this->_execute($sql);
		if ($res && isset($res->fields['mapped_code'])) {
			return $res->fields['mapped_code'];
		}
		return 0.00;
	}

	/**
	 * Set a default value for every code of type 3 or 5 (cpt, cdt)
	 */
	function setDefaultValue($value) {
		$this->_execute("replace into fee_schedule_data (code_id,revision_id,fee_schedule_id,data) select code_id, 1, ".$this->get('fee_schedule_id').", $value from codes where code_type in (3,5)");
	}

	function numCodesWithFees() {
		$id = EnforceType::int($this->get('id'));
		$sql = "select count(*) c from fee_schedule_data fsd where data > 0 and fsd.fee_schedule_id = $id";
		return $this->dbHelper->getOne($sql);
	}

	// figure out the default fee is slightly hackish, but should be correct in any standard system
	function numCodesNotDefaultFee() {
		$id = EnforceType::int($this->get('id'));
		$sql = "select data, count(*) c from fee_schedule_data fsd where data > 0 and fsd.fee_schedule_id = $id group by data order by c DESC limit 1";
		$res = $this->dbHelper->execute($sql);

		$defaultFee = false;
		if ($res->fields['c'] > 200) {
			$defaultFee = $res->fields['data'];
		}

		if ($defaultFee) {
			$sql = "select count(*) c from fee_schedule_data fsd where data > 0 and fsd.fee_schedule_id = $id and data != $defaultFee";
			return $this->dbHelper->getOne($sql);
		}
		else {
			$sql = "select count(*) c from fee_schedule_data fsd where data > 0 and fsd.fee_schedule_id = $id";
			return $this->dbHelper->getOne($sql);
		}
	}
}
?>
