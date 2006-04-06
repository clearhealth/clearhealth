<?php
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Address');
class PersonAddress extends Address {
	var $_relation = "person_address";
	var $_fkey = "person_id";
	
	var $person_id = '';

	function setup($id = 0,$parent = false, $type = "person") {
		//var_dump("new PersonAddress",get_class($this));
		$this->set('person_id', $parent);
		parent::setup($id,$parent,$type);
	}
	
	
	/**
	 * Returns true if this address is tied to multiple people
	 *
	 * @return boolean
	 */
	function value_isMultiple() {
		if (!$this->isPopulated()) {
			return false;
		}
		
		$qAddressId = $this->dbHelper->quote($this->get('address_id'));
		$sql = "SELECT COUNT(*) AS `total` FROM {$this->_relation} WHERE address_id = {$qAddressId}";
		$row = $this->dbHelper->getOne($sql);
		return ($row['total'] > 1);
	}
	
	
	/**
	 * Returns an array of names that this address belongs to, excluding the current person
	 *
	 * @return array
	 */
	function valueList_otherPeople() {
		if (!$this->isPopulated()) {
			return array();
		}
		
		$qCurrentPersonId = $this->dbHelper->quote($this->get('person_id'));
		$qAddressId = $this->dbHelper->quote($this->get('address_id'));
		
		$person =& Celini::newORDO('Person');
		$peopleTable = $person->tableName();
		$peopleIdColumn = $person->primaryKey();
		
		$sql = "
			SELECT 
				p.{$peopleIdColumn} AS id,
				CONCAT(p.first_name, ' ', p.last_name) AS name
			FROM
				{$this->_relation} AS a
				INNER JOIN {$peopleTable} AS p USING(person_id)
			WHERE
				p.{$peopleIdColumn} != {$qCurrentPersonId} AND
				a.address_id = {$qAddressId}";
		$result = $this->dbHelper->execute($sql);
		
		$returnArray = array();
		while ($result && !$result->EOF) {
			$returnArray[$result->fields['id']] = $result->fields['name'];
			
			$result->moveNext();
		}
		
		return $returnArray;
	}
}
