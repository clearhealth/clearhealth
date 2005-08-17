<?php

require_once CELLINI_ROOT . '/includes/Datasource_sql.class.php';

class Person_InsuredRelationshipList_DS extends Datasource_sql
{
	var $_table = 'insured_relationship';
	var $_subscriberRelationships = null;
	
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Person_InsuredRelationshipList_DS';
	
	
	function Person_InsuredRelationshipList_DS($person_id) {
		settype($person_id,'int');
		$this->_db =& Cellini::dbInstance();
		
		$this->setup($this->_db,
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
		$this->addOrderRule('program_order');
		$this->registerFilter('subscriber_relationship',array($this,'lookupSubscriberRelationship'));
		$this->registerFilter('effective',array($this,'effectiveColorFilter'));
	}
	
	
	function effectiveColorFilter($content) {
		$ret = "<div style='margin-left:-5px; text-align: center;";
		if (!strstr($content,'Until')) {
			$ret .= " color: darkred;";
		}
		return $ret .= "'>$content</div>";
	}
	
	
	/**
	 * Look subscriber relationship.
	 *
	 * @return string|null
	 */
	function lookupSubscriberRelationship($id) {
		$this->_loadSubscriberRelationships();
		if (isset($this->_subscriberRelationships[$id])) {
			return $this->_subscriberRelationships[$id];
		}
	}
	

	/**
	 * Loads the subscriber_to_patient_relationship enum
	 *
	 * @access private
	 */
	function _loadSubscriberRelationships() {
		if (!is_null($this->_subscriberRelationships)) {
			return;
		}
		
		$enum = ORDataObject::factory('Enumeration');
		$this->_subscriberRelationships = $enum->get_enum_list('subscriber_to_patient_relationship');
	}
}

