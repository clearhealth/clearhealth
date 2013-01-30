<?php
/*****************************************************************************
*       Practice.php
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


class Practice extends WebVista_Model_ORM {
	protected $id;
	protected $name;
	protected $website;
	protected $identifier;
	protected $primaryAddress;
	protected $secondaryAddress;
	protected $mainPhone;
	protected $secondaryPhone;
	protected $fax;
	protected $_table = 'practices';
	protected $_primaryKeys = array('id');

	protected $_cascadePopulate = false;

	public function __construct() {
		parent::__construct();
		$this->_init();
	}

	protected function _init() {
		$this->primaryAddress = new Address();
		$this->primaryAddress->type = Address::TYPE_MAIN;
		$this->secondaryAddress = new Address();
		$this->secondaryAddress->type = Address::TYPE_SEC;
		$this->mainPhone = new PhoneNumber();
		$this->mainPhone->type = PhoneNumber::TYPE_HOME; // MAIN
		$this->secondaryPhone = new PhoneNumber();
		$this->secondaryPhone->type = PhoneNumber::TYPE_WORK; // SEC
		$this->fax = new PhoneNumber();
		$this->fax->type = PhoneNumber::TYPE_FAX;
	}

	public function getPracticeId() {
		return $this->id;
	}

	public function setId($id) {
		$this->setPracticeId($id);
	}

	public function setPracticeId($id) {
		$this->id = (int)$id;
		if (!$this->id > 0) {
			$this->_init();
			return;
		}
		$this->primaryAddress->practiceId = $this->id;
		if (!$this->primaryAddress->addressId > 0) {
			$address = clone $this->primaryAddress;
			$address->populateWithPracticeIdType();
			$this->primaryAddress->addressId = $address->addressId;
		}
		$this->secondaryAddress->practiceId = $this->id;
		if (!$this->secondaryAddress->addressId > 0) {
			$address = clone $this->secondaryAddress;
			$address->populateWithPracticeIdType();
			$this->secondaryAddress->addressId = $address->addressId;
		}
		$this->mainPhone->practiceId = $this->id;
		if (!$this->mainPhone->numberId > 0) {
			$phone = clone $this->mainPhone;
			$phone->populateWithPracticeIdType();
			$this->mainPhone->numberId = $phone->numberId;
		}
		$this->secondaryPhone->practiceId = $this->id;
		if (!$this->secondaryPhone->numberId > 0) {
			$phone = clone $this->secondaryPhone;
			$phone->populateWithPracticeIdType();
			$this->secondaryPhone->numberId = $phone->numberId;
		}
		$this->fax->practiceId = $this->id;
		if (!$this->fax->numberId > 0) {
			$phone = clone $this->fax;
			$phone->populateWithPracticeIdType();
			$this->fax->numberId = $phone->numberId;
		}
	}

	public function populate() {
		$ret = parent::populate();
		$this->primaryAddress->populateWithPracticeIdType();
		$this->secondaryAddress->populateWithPracticeIdType();
		$this->mainPhone->populateWithPracticeIdType();
		$this->secondaryPhone->populateWithPracticeIdType();
		$this->fax->populateWithPracticeIdType();
		return $ret;
	}

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
			$params['ormClass'] = 'Building';
			return $view->action('edit','enumerations-manager',null,$params);
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['id'] = $ormId;
			return $view->action('edit-practice','facilities',null,$params);
		}
	}

}
