<?php
/*****************************************************************************
*       VisitSelectController.php
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


class VisitSelectController extends WebVista_Controller_Action {

	public function indexAction()  {
		$personId = (int)$this->_getParam('personId');
		$visitId = (int)$this->_getParam('visitId');

		$identity = Zend_Auth::getInstance()->getIdentity();
		$this->view->providerId = (int)$identity->personId;
		$this->view->personId = $personId;

		$insuredRelationship = new InsuredRelationship();
		$insuredRelationship->personId = $personId;
		$insurancePrograms = array('0'=>'');
		foreach ($insuredRelationship->getProgramList() as $key=>$value) {
			$insurancePrograms[$key] = $value;
		}
		$this->view->insurancePrograms = $insurancePrograms;

		$visit = new Visit();
		$visit->visitId = $visitId;
		if (!$visit->populate()) {
			$visit->visitId = 0;
			$visit->dateOfTreatment = date('Y-m-d');
		}
		$this->view->visit = $visit;

		$this->view->room = Building::getBuildingDefaultLocation($this->view->providerId,(int)$identity->default_location_id);

		$facilityIterator = new FacilityIterator();
		$this->view->facilityIterator = $facilityIterator;
		$facilityIterator->setFilter(array('Practice'));
		$this->view->practices = $facilityIterator->toArray('practiceId','name');

		$provider = new Provider();
		$providerIterator = $provider->getIter();
		$this->view->providers = $providerIterator->toArray('personId','displayName');
		$this->render();
	}

	public function visitTypeAction() {
		$this->render();
	}

	public function diagnosesAction() {
		$visitId = (int)$this->_getParam('visitId');
		$visit = new Visit();
		$visit->visitId = $visitId;
		if ($visitId > 0) $visit->populate();
		$this->view->providerId = (int)$visit->providerId;
		$this->render();
	}

	public function proceduresAction() {
		$visitId = (int)$this->_getParam('visitId');
		$visit = new Visit();
		$visit->visitId = $visitId;
		if ($visitId > 0) $visit->populate();
		$provider = new Provider();
		$providerIterator = $provider->getIter();
		$this->view->listProviders = $providerIterator->toArray('personId','displayName');
		$this->view->providerId = (int)$visit->providerId;
		$this->render();
	}

	public function claimAction() {
		$visitId = (int)$this->_getParam('visitId');
		$insurancePrograms = array();
		$insuranceProgramId = 0;
		if ($visitId > 0) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();

			$insuredRelationship = new InsuredRelationship();
			$insuredRelationship->personId = (int)$visit->patientId;
			$insurancePrograms = $insuredRelationship->getProgramList();
			$insuranceProgramId = (int)$visit->activePayerId;
		}
		$this->view->visitId = $visitId;
		$this->view->insurancePrograms = $insurancePrograms;
		$this->view->insuranceProgramId = $insuranceProgramId;
		$this->render();
	}

	public function processSetInsuranceProgramAction() {
		$visitId = (int)$this->_getParam('visitId');
		$payerId = (int)$this->_getParam('payerId');
		$visit = new Visit();
		$visit->visitId = $visitId;
		if ($visit->populate()) {
			$visit->activePayerId = $payerId;
			$visit->persist();
			$visit->syncClaimsInsurance();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($visit->activePayerId);
	}

	public function vitalsAction() {
		$this->render();
	}

	public function immunizationsAction() {
		$enumerationsClosure = new EnumerationsClosure();

		$othersId = 0;
		$series = array();
		$sites = array();
		$sections = array();
		$reactions = array();
		$routes = array();
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
							continue;
						}
						$sections[$item->enumerationId] = $item->name;
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
		$this->view->othersId = $othersId;
		$this->view->series = $series;
		$this->view->sites = $sites;
		$this->view->sections = $sections;
		$this->view->reactions = $reactions;
		$this->view->routes = $routes;
		$config = Zend_Registry::get('config');
		$this->view->useImmunizationInventory = ((string)$config->useImmunizationInventory == 'true')?true:false;
		$this->render();
	}

	public function educationAction() {
		$enumerationsClosure = new EnumerationsClosure();

		$othersId = 0;
		$levels = array();
		$sections = array();
		$parentName = PatientEducation::ENUM_EDUC_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName($parentName);
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		foreach ($enumerationIterator as $enum) {
			switch ($enum->name) {
				case PatientEducation::ENUM_EDUC_LEVEL_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					foreach ($enumIterator as $item) {
						$levels[$item->enumerationId] = $item->name;
					}
					break;
				case PatientEducation::ENUM_EDUC_SECTION_NAME:
					$enumIterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
					foreach ($enumIterator as $item) {
						if ($item->name == PatientEducation::ENUM_EDUC_SECTION_OTHER_NAME) {
							$othersId = $item->enumerationId;
							continue;
						}
						$sections[$item->enumerationId] = $item->name;
					}
					break;
			}
		}
		$this->view->othersId = $othersId;
		$this->view->levels = $levels;
		$this->view->sections = $sections;

		$this->render();
	}

	public function hsaAction() {
		$this->render();
	}

	public function examsAction() {
		$name = PatientExam::ENUM_RESULT_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		foreach ($enumerationIterator as $enumeration) {
			$listResults[$enumeration->key] = $enumeration->name;
		}
		$this->view->listResults = $listResults;
		$this->render();
	}

	protected function _generateVisitRowData(Visit $visit) {
		$row = array();
		$row['id'] = $visit->visitId;
		$row['data'][] = date('Y-m-d',strtotime($visit->dateOfTreatment));
		$row['data'][] = $visit->locationName;
		$row['data'][] = $visit->providerDisplayName;
		$row['data'][] = $visit->insuranceProgram;
		// hidden columns
		$row['data'][] = (int)$visit->practiceId;
		$row['data'][] = (int)$visit->buildingId;
		$row['data'][] = (int)$visit->roomId;
		$row['data'][] = (int)$visit->treatingPersonId;
		$row['data'][] = (int)$visit->activePayerId;
		$row['data'][] = (int)$visit->closed;
		$row['data'][] = (int)$visit->void;
		$row['data'][] = ucwords($visit->displayStatus);
		return $row;
	}

	public function listVisitsAction() {
		$personId = (int)$this->_getParam('personId');
		if (!$personId > 0) $this->_helper->autoCompleteDojo(array());
		$visitIterator = new VisitIterator();
		$visitIterator->setFilters(array('patientId' => $personId));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$rows = array();
		foreach ($visitIterator as $visit) {
			$rows[] = $this->_generateVisitRowData($visit);
		}
		$json->direct(array('rows' => $rows),true);
        }

	public function processSaveVisitAction() {
		$visitParams = $this->_getParam('visit');
		$visitParams['createdByUserId'] = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$visitParams['timestamp'] = date('Y-m-d h:i:s');
		$visit = new Visit();
		$visitId = (int)$visitParams['visitId'];
		if ($visitId > 0) {
			$visit->visitId = $visitId;
			$visit->populate();
		}
		$visit->populateWithArray($visitParams);
		$visit->persist();
		$data = $this->_generateVisitRowData($visit);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _processSetVisit($closed=null,$void=null) {
		$visitParams = $this->_getParam('visit');
		$visitParams['lastChangeUserId'] = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		//$visitParams['timestamp'] = date('Y-m-d h:i:s');
		if ($closed !== null) {
			$visitParams['closed'] = (int)$closed;
		}
		$visit = new Visit();
		$visit->visitId = (int)$visitParams['visitId'];
		$data = 'Visit ID '.$visit->visitId.' is invalid';
		if ($visit->populate()) {
			if ($void !== null) {
				$visit->void = (int)$void;
				if ($visit->void && $visit->hasPayments()) {
					$error = 'Cannot void visit with payments';
				}
			}
			else {
				$visit->populateWithArray($visitParams);
			}
			if (isset($error)) {
				$data = $error;
			}
			else {
				$visit->persist();
				$data = $this->_generateVisitRowData($visit);
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSaveAndCloseVisitAction() {
		$this->_processSetVisit(1);
	}

	public function processSaveAndReopenVisitAction() {
		$this->_processSetVisit(0);
	}

	public function processVoidVisitAction() {
		$this->_processSetVisit(null,1);
	}

	public function visitDetailsAction() {
		$this->render();
	}

}
