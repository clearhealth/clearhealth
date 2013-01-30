<?php
/*****************************************************************************
*       ImmunizationInventory.php
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


class ImmunizationInventory extends WebVista_Model_ORM {

	protected $immunizationInventoryId;
	protected $immunization;
	protected $lotNumber;
	protected $expiration;
	protected $parLevel;
	protected $manufacturer;
	protected $mvxCode; // manufacturer code
	protected $immunizationId;

	protected $_primaryKeys = array('immunizationInventoryId');
	protected $_table = 'immunizationInventory';

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		$config = Zend_Registry::get('config');
		if ($isAdd || $config->useImmunizationInventory == 'false') {
			$params['parentId'] = $enumerationId;
			if ($isAdd) unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
			return $view->action('edit','enumerations-manager',null,$params);
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['ormId'] = $ormId;
			return $view->action('edit','immunization-inventory',null,$params);
		}
	}

	public function getTotalInStock() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'COUNT(immunizationInventoryId) AS total')
				->where('immunizationId IS NULL OR immunizationId = 0')
				->where('UNIX_TIMESTAMP(expiration) >= UNIX_TIMESTAMP(NOW())')
				->where('immunization = ?',(string)$this->immunization);
		$ret = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = (int)$row['total'];
		}
		return $ret;
	}

	public function getTotalExpired() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'COUNT(immunizationInventoryId) AS total')
				->where('immunizationId IS NULL OR immunizationId = 0')
				->where('UNIX_TIMESTAMP(expiration) < UNIX_TIMESTAMP(NOW())')
				->where('immunization = ?',(string)$this->immunization);
		$ret = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = (int)$row['total'];
		}
		return $ret;
	}

	public static function setDefaultParLevel($immunization,$parLevel) {
		$orm = new self();
		$db = Zend_Registry::get('dbAdapter');
		$sql = 'UPDATE `'.$orm->_table.'` SET `parLevel` = '.(int)$parLevel.' WHERE (`immunization` = '.$db->quote((string)$immunization).')';
		return $db->query($sql);
	}

	public function populateByImmunizationId($immunizationId = null) {
		if ($immunizationId === null) $immunizationId = $this->immunizationId;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('immunizationId = ?',(int)$immunizationId)
				->order('immunizationInventoryId DESC')
				->limit(1);
		$ret = $this->populateWithSql($sqlSelect->__toString());
		$this->postPopulate();
		return $ret;
	}

	public function populateByImmunization($immunization = null) {
		if ($immunization === null) $immunization = $this->immunization;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('immunization = ?',(string)$immunization)
				->order('immunizationInventoryId DESC')
				->limit(1);
		$ret = $this->populateWithSql($sqlSelect->__toString());
		$this->postPopulate();
		return $ret;
	}

}
