<?php
/**
 * Object Relational Persistence Mapping Class for table: insured_relationship
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: insured_relationship
 *
 * @package	com.uversainc.clearhealth
 */
class InsuredRelationship extends ORDataObject {

	/**#@+
	 * Fields of table: insured_relationship mapped to class members
	 */
	var $id					= '';
	var $insurance_program_id		= '';
	var $person_id				= '';
	var $subscriber_id			= '';
	var $subscriber_to_patient_relationship	= '';
	var $copay				= '';
	var $assigning				= '';
	var $group_name				= '';
	var $group_number			= '';
	var $default_provider			= '';
	var $program_order			= '';
	var $effective_start			= '';
	var $effective_end			= '';
	var $active					= 1;
	/**#@-*/
	var $_table = 'insured_relationship';
	var $_internalName='InsuredRelationship';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function InsuredRelationship($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'insured_relationship';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Insured_relationship with this
	 */
	function setup($id = 0,$person_id = false) {
		if ($person_id > 0) {
			$this->set('person_id',$person_id);
		}

		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	function setupByGroup($person_id,$group_name,$group_number) {
		$p = EnforceType::int($person_id);
		$g = $this->dbHelper->quote($group_name);
		$gn = $this->dbHelper->quote($group_number);
		$sql = "select * from ".$this->tableName()." where person_id = $p and group_name = $g and group_number = $gn";

		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this,$res);
	}

	function setupByInsuranceProgram($program_id) {
		$id = EnforceType::int($program_id);
		$sql = "select * from ".$this->tableName()." where insurance_program_id = $id";
		$this->helper->populateFromQuery($this, $sql);
	}

	/**
	 * return an array of InsuredRelationship objects that correspond to a personid
	 */
	function &fromPersonId($person_id) {
		//echo "Insured Relationship: fromPersonid with $person_id<br>";
		settype($person_id,'int');
		$ret = array();

		$ir =& ORDataObject::Factory('InsuredRelationship');
		$sql = "select * from $ir->_table where person_id = $person_id order by program_order";
		//echo "<br> $sql <br>";
		$res = $ir->_execute($sql);
			

		$i = 0;
		while($res && !$res->EOF) {
			$ret[$i] = new InsuredRelationship();
			$ret[$i]->populate_array($res->fields);
			$res->MoveNext();
			$i++;
		}
		return $ret;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('insured_relationship_id');
	}

	function persist() {
		if (empty($this->program_order)) {
			// we don't have an order set figure out what it should be
			$res = $this->_execute("select max(program_order)+1 po from $this->_table where person_id = ".(int)$this->get('person_id'));
			if ($res && isset($res->fields['po'])) {
				$this->set('program_order',$res->fields['po']);
			}
			else {
				$this->set('program_order',1);
			}
		}
		parent::persist();
	}

	/**#@+
	 * Getters and Setters for Table: insured_relationship
	 * @access protected
	 */

	
	/**
	 * Getter for Primary Key: insured_relationshp_id
	 */
	function get_insured_relationship_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: insured_relationshp_id
	 */
	function set_insured_relationship_id($id)  {
		$this->id = $id;
	}

	/**
	 * automatically sets person_id to the current patient when the relationship is Self
	 * for this to work person_id has to be set befor the relationship
	 */
	function set_subscriber_to_patient_relationship($val) {
		if ($this->lookupSubscriberRelationship($val) === "Self") {
			$this->set('subscriber_id',$this->get('person_id'));
		}
		$this->subscriber_to_patient_relationship = $val;
	}

	function get_subscriber_to_patient_relationship_name() {
		return $this->lookupSubscriberRelationship($this->get('subscriber_to_patient_relationship'));
	}
	
	
	/**
	 * Insures that effective start date is in ISO format for database storage
	 *
	 */
	function set_effective_start($date) {
		$this->_setDate('effective_start', $date);
	}
	
	/**
	 * Returns effective start date as an English date instead of ISO
	 *
	 * @return string
	 */
	function get_effective_start() {
		return $this->_getDate('effective_start');
	}
	
	/**
	 * Insures that effective end date is in ISO format for database storage
	 *
	 */
	function set_effective_end($date) {
		$this->_setDate('effective_end', $date);
	}
	
	/**
	 * Returns effective start date as an English date instead of ISO
	 *
	 * @return string
	 */
	function get_effective_end() {
		return $this->_getDate('effective_end');
	}

	/**
	 * Get the insurance companies name
	 */
	function get_insurance_company_name() {
		$program =& ORDataObject::Factory('InsuranceProgram',$this->get('insurance_program_id'));
		return $program->get('insurance_company_name');
	}

	/**
	 * Get the program name
	 */
	function get_program_name() {
		$program =& ORDataObject::Factory('InsuranceProgram',$this->get('insurance_program_id'));
		return $program->get('name');
	}

	function get_subscriber_print() {
	}
	
	/**#@-*/

	function insuredRelationshipList($person_id) {
		settype($person_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,
			array('cols' 	=> "ir.insured_relationship_id,
				ir.insurance_program_id, 
				group_name,
				group_number,
				copay,
				ip.name as program,
				c.name as company,
				program_order,
				subscriber_to_patient_relationship subscriber_relationship, 
				if(now() between effective_start and effective_end,concat('Until ',DATE_FORMAT(effective_end, '%m/%d/%Y')),
				if (effective_end < now(),concat('Ended ',DATE_FORMAT(effective_end, '%m/%d/%Y')),concat('Starts ',DATE_FORMAT(effective_start, '%m/%d/%Y')))) effective,
				active",
				'from' 	=> "$this->_table ir left join insurance_program ip using (insurance_program_id) left join company c using (company_id)",
				'where' => " person_id = $person_id",
			),
			array('program_order' => false,
				'company'=> 'Company',
				'program' => "Program",
				'group_name' => 'Group Name',
				'group_number'=> 'Group Number',
				'copay' => 'Co-pay',
				'subscriber_relationship' => 'Subscriber',
				'effective'=>'Effective', 
				'active' => 'Active'));
		$ds->addOrderRule('program_order');
		$ds->registerFilter('subscriber_relationship',array($this,'lookupSubscriberRelationship'));
		$ds->registerFilter('effective',array($this,'effectiveColorFilter'));
		return $ds;
	}


	function getInsurerList() {
		$res = $this->_execute("select company_id, name from company");
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['company_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}

	function getProgramList($person_id) {
		settype($person_id,'int');
		$sql = "select 
				ip.insurance_program_id id, 
				concat_ws('->',c.name,ip.name) name 
			from $this->_table 
				inner join insurance_program ip using(insurance_program_id)
				left join company c using(company_id)
			where
				person_id = $person_id AND
				active = 1
			order by
				program_order";

		$res = $this->_execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['id']] = $res->fields['name'];
			$res->moveNexT();
		}
		return $ret;
	}

	function getAssigningList() {
		$list = $this->_load_enum('assigning',false);
		return array_flip($list);
	}

	function getSubscriberToPatientRelationshipList() {
		$em =& Celini::enumManagerInstance();
		return $em->enumArray('subscriber_to_patient_relationship');
	}

	var $_Cache = false;
	/**
	 * Cached lookup for person_type
	 */
	function lookupSubscriberRelationship($id) {
		if ($this->_Cache === false) {
			$this->_Cache = $this->getSubscriberToPatientRelationshipList();
		}
		if (isset($this->_Cache[$id])) {
			return $this->_Cache[$id];
		}
	}

	function numRelationships($person_id) {
		settype($person_id,'int');
		$res = $this->_execute("select count(*) c from $this->_table where person_id = $person_id");
		if ($res && isset($res->fields['c'])) {
			return $res->fields['c'];
		}
	}

	function moveDown() {
		$this->_execute("update $this->_table set program_order = program_order -1 where program_order = ".($this->get('program_order') +1)
					." and person_id = ".(int)$this->get('person_id'));
		$this->_execute("update $this->_table set program_order = program_order +1 where insured_relationship_id = ".(int)$this->get('id'));
	}

	function moveUp() {
		$this->_execute("update $this->_table set program_order = program_order +1 where program_order = ".($this->get('program_order') -1)
					." and person_id = ".(int)$this->get('person_id'));
		$this->_execute("update $this->_table set program_order = program_order -1 where insured_relationship_id = ".(int)$this->get('id'));
	}

	function toArray() {
		$ret = array();
		$ret['group_name'] = $this->get('group_name');
		$ret['group_number'] = $this->get('group_number');
		$ret['relationship'] = $this->lookupSubscriberRelationship($this->get('subscriber_to_patient_relationship'));
		$subscriber =& ORDataObject::factory('Person',$this->get('subscriber_id'));
		$ret['subscriber'] = $subscriber->toArray();
		return $ret;
	}

	function effectiveColorFilter($content) {
		$ret = "<div style='margin-left:-5px; text-align: center;";
		if (!strstr($content,'Until')) {
			$ret .= " color: darkred;";
		}
		return $ret .= "'>$content</div>";
	}
}
?>
