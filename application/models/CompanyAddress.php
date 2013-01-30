<?php
/*****************************************************************************
*       CompanyAddress.php
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


class CompanyAddress extends WebVista_Model_ORM {

	protected $company_id;
	protected $address_id;
	protected $address;
	protected $address_type;

	protected $_table = 'company_address';
	protected $_primaryKeys = array('company_id','address_id');
	protected $_legacyORMNaming = true;

	public function __construct() {
		parent::__construct();
		$this->address = new Address();
	}

	public function persist() {
		$cascadePersist = null;
		if (!$this->addressId > 0) {
			$this->address->persist();
			$this->addressId = $this->address->addressId;
			$cascadePersist = $this->address->_cascadePersist;
			$this->address->_cascadePersist = false;
		}
		parent::persist();
		if ($cascadePersist !== null) {
			$this->address->_cascadePersist = $cascadePersist;
		}
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->address->ORMFields())) {
			return $this->address->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->address->__get($key))) {
			return $this->address->__get($key);
		}
		return parent::__get($key);
	}

	public function __set($key,$value) {
		if (in_array($key,$this->ORMFields())) {
			return parent::__set($key,$value);
		}
		elseif (in_array($key,$this->address->ORMFields())) {
			return $this->address->__set($key,$value);
		}
		return parent::__set($key,$value);
	}

}
