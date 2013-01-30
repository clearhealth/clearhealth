<?php
/*****************************************************************************
*       Company.php
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


class Company extends WebVista_Model_ORM {

	protected $company_id;
	protected $name;
	protected $description;
	protected $notes;
	protected $initials;
	protected $url;
	protected $is_historic; // yes/no
	protected $_companyEmail;

	protected $_table = 'company';
	protected $_primaryKeys = array('company_id');
	protected $_legacyORMNaming = true;

	public function populate() {
		$ret = parent::populate();
		$storageString = new StorageString();
		$storageString->foreignKey = $this->companyId;
		$storageString->valueKey = 'email';
		$storageString->arrayIndex = 1; // start index at 1, may have problem with ORM
		$storageString->populate();
		$this->_companyEmail = $storageString->value;
		return $ret;
	}

	public function persist() {
		parent::persist();
		$storageString = new StorageString();
		$storageString->setPersistMode($this->_persistMode);
		$storageString->foreignKey = $this->companyId;
		$storageString->valueKey = 'email';
		$storageString->value = $this->_companyEmail;
		$storageString->arrayIndex = 1; // start index at 1, may have problem with ORM
		$storageString->persist();
	}

	public function getIterator($dbSelect = null) {
		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
				       ->from($this->_table)
				       ->order('name');
		}
		return parent::getIterator($dbSelect);
	}

	public function getPhoneNumberIterator($companyId = null) {
		if ($companyId === null) {
			$companyId = $this->companyId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from(array('cn'=>'company_number'))
			       ->join(array('n'=>'number'),'n.number_id = cn.number_id')
			       ->where('cn.company_id = ?',(int)$companyId);
		return new PhoneNumberIterator($dbSelect);
	}

	public function getAddressIterator($companyId = null) {
		if ($companyId === null) {
			$companyId = $this->companyId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from(array('ca'=>'company_address'))
			       ->join(array('a'=>'address'),'a.address_id = ca.address_id')
			       ->where('ca.company_id = ?',(int)$companyId);
		return new AddressIterator($dbSelect);
	}

}
