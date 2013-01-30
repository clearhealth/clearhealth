<?php
/*****************************************************************************
*       AddressIterator.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class AddressIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null) {
		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
				       ->from('address');
		}
		parent::__construct("Address",$dbSelect);
	}

	function setFilters(array $filters) {
		foreach ($filters as $filter => $value) {
			switch ($filter) {
				case 'class':
					switch($value) {
						case 'person':
							$this->_dbSelect->join('person_address','address.address_id = person_address.address_id', 'person_address.address_type as type');
						break;
						case 'practice':
							$this->_dbSelect->join('practice_address','address.address_id = practice_address.address_id', 'practice_address.address_type as type');
						break;
					}
				break;
				case 'personId':
					$this->_dbSelect->where('person_address.person_id = ' . (int)$value);
				break;
				case 'practiceId':
					$this->_dbSelect->where('practice_address.practice_id = ' . (int)$value);
				break;
				case 'addressType':
					$this->_dbSelect->where('address_type = ' . preg_replace('/[^A-Za-z0-9]/','',$value));
				break;
			}
		}
		//echo $this->_dbSelect->__toString();exit;
	}

	public function setDbSelect($dbSelect) {
		$db = Zend_Registry::get('dbAdapter');
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

	public function current() {
		$ormObj = new $this->_ormClass();
		$row = $this->_dbStmt->fetch(null,null,$this->_offset);
		if (isset($row['address_type']) && (int)$row['address_type'] > 0) {
			$row['type'] = $row['address_type'];
		}
		$ormObj->populateWithArray($row);
		return $ormObj;
	}

}
