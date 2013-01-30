<?php
/*****************************************************************************
*       CompanyNumber.php
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


class CompanyNumber extends WebVista_Model_ORM {

	protected $company_id;
	protected $number_id;
	protected $number;

	protected $_table = 'company_number';
	protected $_primaryKeys = array('company_id','number_id');
	protected $_legacyORMNaming = true;

	public function __construct() {
		parent::__construct();
		$this->number = new PhoneNumber();
	}

	public function persist() {
		$cascadePersist = null;
		if (!$this->numberId > 0) {
			$this->number->persist();
			$this->numberId = $this->number->numberId;
			$cascadePersist = $this->number->_cascadePersist;
			$this->number->_cascadePersist = false;
		}
		parent::persist();
		if ($cascadePersist !== null) {
			$this->number->_cascadePersist = $cascadePersist;
		}
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->number->ORMFields())) {
			return $this->number->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->number->__get($key))) {
			return $this->number->__get($key);
		}
		return parent::__get($key);
	}

	public function __set($key,$value) {
		if (in_array($key,$this->ORMFields())) {
			return parent::__set($key,$value);
		}
		elseif (in_array($key,$this->number->ORMFields())) {
			return $this->number->__set($key,$value);
		}
		return parent::__set($key,$value);
	}

	public function getCompanyNumberId() {
		return $this->company_id;
	}

}
