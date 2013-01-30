<?php
/*****************************************************************************
*       ImmunizationInventoryController.php
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


class ImmunizationInventoryController extends WebVista_Controller_Action {

	public function editAction() {
		$ormId = (int)$this->_getParam('ormId');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$enumeration = new Enumeration();
		$enumeration->enumerationId = $enumerationId;
		$enumeration->populate();
		$inventory = new ImmunizationInventory();
		$inventory->immunization = $enumeration->name;
		$inventory->expiration = date('Y-m-d',strtotime('+1 year'));
		$form = new WebVista_Form(array('name'=>'edit'));
		$form->setAction(Zend_Registry::get('baseUrl').'immunization-inventory.raw/process-edit');
		$form->loadORM($inventory,'inventory');
		$form->setWindow('windowEditORMObjectId');
		$this->view->form = $form;
		$this->view->enumerationId = $enumerationId;
		$this->view->totalInStock = 0; //$inventory->totalInStock;
		$this->view->totalExpired = 0; //$inventory->totalExpired;
		$this->render('edit');
	}

	public function processEditAction() {
		$params = $this->_getParam('inventory');
		$inventory = new ImmunizationInventory();
		if (isset($params['immunizationInventoryId'])) {
			$inventory->immunizationInventoryId = (int)$params['immunizationInventoryId'];
			$inventory->populate();
		}
		$inventory->populateWithArray($params);
		$inventory->persist();
		$data = $this->_generateImmunizationInventoryRowData($inventory);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateImmunizationInventoryRowData(ImmunizationInventory $inventory) {
		$row = array();
		$row['id'] = $inventory->immunizationInventoryId;
		$row['data'] = array();
		$row['data'][] = (string)$inventory->manufacturer;
		$row['data'][] = (string)$inventory->mvxCode;
		$row['data'][] = (string)$inventory->lotNumber;
		$row['data'][] = date('Y-m-d',strtotime($inventory->expiration));
		$row['data'][] = (int)$inventory->immunizationId;
		return $row;
	}

	public function processAddAction() {
		$quantity = (int)$this->_getParam('quantity');
		if (!$quantity > 0) $quantity = 1;
		$params = $this->_getParam('inventory');
		$data = false;
		if (is_array($params)) {
			for ($i = 0; $i < $quantity; $i++) {
				$inventory = new ImmunizationInventory();
				$inventory->populateWithArray($params);
				$inventory->persist();
			}
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listAction() {
		$immunization = $this->_getParam('immunization');
		$rows = array();
		$iterator = new ImmunizationInventoryIterator();
		$iterator->setFilters(array('immunization'=>$immunization));
		foreach ($iterator as $inventory) {
			if (!isset($parLevel)) $parLevel = (int)$inventory->parLevel;
			$rows[] = $this->_generateImmunizationInventoryRowData($inventory);
		}
		if (isset($parLevel)) $rows[0]['userdata']['parLevel'] = $parLevel;
		$data = array('rows'=>$rows);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$ids = $this->_getParam('ids');
		$data = false;
		foreach (explode(',',$ids) as $id) {
			$id = (int)$id;
			if (!$id > 0) continue;
			$inventory = new ImmunizationInventory();
			$inventory->immunizationInventoryId = $id;
			if (!$inventory->populate() || $inventory->immunizationId > 0) continue;
			$inventory->setPersistMode(WebVista_Model_ORM::DELETE);
			$inventory->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSetParLevelAction() {
		$immunization = $this->_getParam('immunization');
		$parLevel = $this->_getParam('parLevel');
		$data = ImmunizationInventory::setDefaultParLevel($immunization,$parLevel);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

}
