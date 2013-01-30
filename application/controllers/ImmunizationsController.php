<?php
/*****************************************************************************
*       ImmunizationsController.php
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


/**
 * Immunization controller
 */
class ImmunizationsController extends WebVista_Controller_Action {

	public function selectionListAction() {
		$id = (int)$this->_getParam('id');
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($id,1);
		$lists = array();
		foreach ($enumerationIterator as $enum) {
			$lists[$enum->key] = $enum->name;
		}
		$this->view->jsCallback = $this->_getParam('jsCallback','');
		$this->view->lists = $lists;
		$this->render();
	}

	public function immunizationsListAction() {
		$immunization = new ProcedureCodesImmunization();
		$immunizationIterator = $immunization->getIterator();
		$data = array();
		$data['rows'] = $immunizationIterator->toJsonArray('code',array('textShort','code'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processPatientImmunizationAction() {
		$patientId = (int)$this->_getParam("patientId");
		$immunizations = $this->_getParam("immunizations");
		if ($patientId > 0) {
			$patientImmunizationIterator = new PatientImmunizationIterator();
			$filter = array();
			$filter['patientId'] = $patientId;
			$patientImmunizationIterator->setFilter($filter);
			$existingImmunizations = $patientImmunizationIterator->toArray('patientImmunizationId','immunization');
			if (is_array($immunizations)) {
				foreach ($immunizations as $patientImmunizationId=>$immunization) {
					if (isset($existingImmunizations[$patientImmunizationId])) {
						unset($existingImmunizations[$patientImmunizationId]);
					}
					$patientImmunization = new PatientImmunization();
					$immunization['patientImmunizationId'] = $patientImmunizationId;
					$immunization['patientId'] = $patientId;
					trigger_error(print_r($immunization,true),E_USER_NOTICE);
					$patientImmunization->populateWithArray($immunization);
					$patientImmunization->administeredDate = date('Y-m-d H:i:s');
					$patientImmunization->persist();
				}
			}
			// delete un-used records
			foreach ($existingImmunizations as $patientImmunizationId=>$immunization) {
				$patientImmunization = new PatientImmunization();
				$patientImmunization->patientImmunizationId = $patientImmunizationId;
				$patientImmunization->setPersistMode(WebVista_Model_ORM::DELETE);
				$patientImmunization->persist();
			}
		}
		$data = array();
		$data['msg'] = __("Record saved successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateImmunizationRowData(PatientImmunization $pi) {
		$config = Zend_Registry::get('config');
		$useImmunizationInventory = ((string)$config->useImmunizationInventory == 'true')?true:false;
		if ($useImmunizationInventory) {
			$lotNumbers = array();
			$rows = array();
			$iterator = new ImmunizationInventoryIterator();
			$iterator->setFilters(array('immunization'=>preg_replace('/&#44;/',',',$pi->immunization),'inStock'=>true,'linked'=>false,'includeRow'=>$pi->lot));
			foreach ($iterator as $inventory) {
				$lotNumbers[$inventory->immunizationInventoryId] = $inventory->lotNumber;
			}
		}
		$ret = array();
		$ret['id'] = $pi->patientImmunizationId;
		$ret['data'][] = date('Y-m-d H:i',strtotime($pi->dateAdministered));
		$ret['data'][] = $pi->lot;
		$ret['data'][] = $pi->amount;
		$ret['data'][] = $pi->units;
		$ret['data'][] = $pi->route;
		$ret['data'][] = $pi->site;
		$ret['data'][] = $pi->series;
		$ret['data'][] = $pi->reaction;
		$ret['data'][] = $pi->immunization;
		$ret['data'][] = (int)$pi->patientReported;
		$ret['data'][] = $pi->comment;
		if ($useImmunizationInventory) $ret['userdata']['lotNumbers'] = $lotNumbers;
		return $ret;
	}

	public function listPatientImmunizationsJsonAction() {
		$patientId = (int)$this->_getParam("patientId");
		$rows = array();
		if ($patientId > 0) {
			$patientImmunizationIterator = new PatientImmunizationIterator();
			$filter = array();
			$filter['patientId'] = $patientId;
			$patientImmunizationIterator->setFilter($filter);
			foreach ($patientImmunizationIterator as $pi) {
				$rows[] = $this->_generateImmunizationRowData($pi);
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processEditImmunizationAction() {
		$params = $this->_getParam('immunizations');
		$patientImmunization = new PatientImmunization();
		$config = Zend_Registry::get('config');
		$useImmunizationInventory = ((string)$config->useImmunizationInventory == 'true')?true:false;
		if (isset($params['patientImmunizationId'])) {
			$patientImmunization->patientImmunizationId = (int)$params['patientImmunizationId'];
			$patientImmunization->populate();
			if ($useImmunizationInventory && isset($params['lot']) && $params['lot'] != $patientImmunization->lot) {
				$inventory = new ImmunizationInventory();
				$inventory->immunizationInventoryId = (int)$patientImmunization->lot;
				$inventory->populate();
				$inventory->immunizationId = 0;
				$inventory->persist();
			}
		}
		else {
			$patientImmunization->dateAdministered = date('Y-m-d');
		}
		$patientImmunization->populateWithArray($params);
		$patientImmunization->persist();
		if (isset($inventory) && $inventory->immunizationId === 0) {
			$inventory2 = new ImmunizationInventory();
			$inventory2->immunizationInventoryId = (int)$params['lot'];
			$inventory2->populate();
			$inventory2->immunizationId = $patientImmunization->patientImmunizationId;
			$inventory2->persist();
		}
		$data = $this->_generateImmunizationRowData($patientImmunization);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteImmunizationAction() {
		$params = $this->_getParam('id');
		$patientImmunization = new PatientImmunization();
		$patientImmunization->setPersistMode(WebVista_Model_ORM::DELETE);
		foreach (explode(',',$params) as $id) {
			$patientImmunization->patientImmunizationId = $id;
			$patientImmunization->persist();
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function immunizationContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render();
	}

	public function listImmunizationSectionNameAction() {
		$sectionId = (int)$this->_getParam('sectionId');
		if (!$sectionId > 0) $this->_helper->autoCompleteDojo(array());
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($sectionId,1);
		$rows = array();
		foreach ($enumerationIterator as $enum) {
			$tmp = array();
			$tmp['id'] = $enum->key;
			$tmp['data'][] = '';
			$tmp['data'][] = $enum->name;
			$tmp['data'][] = $enum->key;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows' => $rows),true);
	}

	public function bulkEntryAction() {
		$othersId = 0;
		$series = array();
		$sites = array();
		$reactions = array();
		$routes = array();
		$enumerationsClosure = new EnumerationsClosure();
		$parentName = PatientImmunization::ENUM_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName($parentName);
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		foreach ($enumerationIterator as $enum) {
			switch ($enum->name) {
				case PatientImmunization::ENUM_SERIES_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					$series = $enumIterator->toArray('key','name');
					break;
				case PatientImmunization::ENUM_BODY_SITE_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					$sites = $enumIterator->toArray('key','name');
					break;
				case PatientImmunization::ENUM_SECTION_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					foreach ($enumIterator as $item) {
						if ($item->name == PatientImmunization::ENUM_SECTION_OTHER_NAME) {
							$othersId = $item->enumerationId;
							break;
						}
					}
					break;
				case PatientImmunization::ENUM_REACTION_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					$reactions = $enumIterator->toArray('key','name');
					break;
				case PatientImmunization::ENUM_ADMINISTRATION_ROUTE_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					$routes = $enumIterator->toArray('key','name');
					break;


			}
		}
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($othersId,1);
		$lists = array();
		foreach ($enumerationIterator as $enum) {
			$lists[$enum->key] = $enum->name;
		}
		$this->view->lists = $lists;

		$this->view->series = $series;
		$this->view->sites = $sites;
		$this->view->reactions = $reactions;
		$this->view->routes = $routes;
		$this->render();
	}

	public function processBulkEntryAction() {
		$params = $this->_getParam('immunizations');
		$config = Zend_Registry::get('config');
		$useImmunizationInventory = ((string)$config->useImmunizationInventory == 'true')?true:false;
		$data = false;
		if (is_array($params)) {
			foreach ($params as $key=>$values) {
				$patientImmunization = new PatientImmunization();
				$patientImmunization->populateWithArray($values);
				$patientImmunization->dateAdministered = date('Y-m-d H:i',strtotime($patientImmunization->dateAdministered));
				$patientImmunization->persist();
				if ($useImmunizationInventory && strlen($patientImmunization->lot) > 0) {
					$inventory = new ImmunizationInventory();
					$inventory->immunization = $patientImmunization->immunization;
					$inventory->populateByImmunization();
					$inventory->immunization = $patientImmunization->immunization;
					$inventory->immunizationInventoryId = 0;
					$inventory->lotNumber = $patientImmunization->lot;
					$inventory->expiration = '';
					$inventory->manufacturer = '';
					$inventory->mvxCode = '';
					$inventory->immunizationId = $patientImmunization->patientImmunizationId;
					$inventory->persist();

					$patientImmunization->lot = $inventory->immunizationInventoryId;
					$patientImmunization->persist();
				}
			}
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
