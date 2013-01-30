<?php
/*****************************************************************************
*       VisitDetailsController.php
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


class VisitDetailsController extends WebVista_Controller_Action {

	public function editTypeAction() {
		$ormClasses = Visit::ormClasses();
		$enumerationParentId = 0;
		$enumerationId = 0;
		$enumeration = new Enumeration();

		$isAdd = (int)$this->_getParam('isAdd');
		if ($isAdd) {
			$parentId = (int)$this->_getParam('parentId');
			$enumParent = new Enumeration();
			$enumParent->enumerationId = $parentId;
			$enumParent->populate();
			$enumeration->ormClass = $enumParent->ormClass;
			$enumerationParentId = $parentId;
		}
		else {
			$enumerationId = (int)$this->_getParam('enumerationId');
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();

			$closure = new EnumerationClosure();
			$parentId = (int)$closure->getParentById($enumerationId);
			if ($parentId === 0) {
				$message = __('There is nothing to edit on this section, add item beneath it');
			}
		}

		if (isset($message)) {
			$this->view->message = $message;
		}
		else {
			$disableTypes = false;
			if ($enumeration->ormClass != 'Visit') {
				$disableTypes = true;
			}
			$form = new WebVista_Form(array('name'=>'visitTypeId'));
			$form->setAction(Zend_Registry::get('baseUrl').'visit-details.raw/process-edit-type');
			$form->loadORM($enumeration,'visit');
			$form->setWindow('windowEditORMObjectId');
			$this->view->form = $form;

			$this->view->disableTypes = $disableTypes;
			$this->view->ormClasses = $ormClasses;
			$this->view->enumerationParentId = $enumerationParentId;
		}

		$this->view->enumerationId = $enumerationId;
		$this->render();
	}

	public function processEditTypeAction() {
		$parentId = (int)$this->_getParam('enumerationParentId');
		$params = $this->_getParam('visit');
		$enumerationId = (int)$params['enumerationId'];
		$ormClass = $params['ormClass'];
		$ormClasses = Visit::ormClasses();
		$data = false;
		if (isset($ormClasses[$ormClass])) {
			if ($parentId > 0) {
				$closure = new EnumerationsClosure();
				$params['active'] = 1;
				$enumerationId = $closure->insertEnumeration($params,$parentId);
			}
			else {
				$enumeration = new Enumeration();
				$enumeration->enumerationId = $enumerationId;
				$enumeration->populate();
				$enumeration->populateWithArray($params);
				$enumeration->persist();
			}
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editVisitTypeAction() {
		$ormId = $this->_getParam('ormId');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$enumerationsClosure = new EnumerationsClosure();
		$depth = (int)$enumerationsClosure->getDepthById($enumerationId);
		$ormClasses = Visit::ormClasses();
		if ($depth > 2) {
			$enumeration = new Enumeration();
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();
			$ormClass = $enumeration->ormClass;
			if (!in_array($ormClass,$ormClasses)) {
				$ormClass = $ormClasses[0]; // temporary set to ProcedureCodesCPT as default ORM Class
			}
			$orm = new $ormClass();
			$orm->code = $ormId;
			$orm->populate();
			$form = new WebVista_Form(array('name'=>'visitTypeId'));
			$form->setAction(Zend_Registry::get('baseUrl').'visit-details.raw/process-edit-visit-type');
			$form->loadORM($orm,'visit');
			$form->setWindow('windowEditORMObjectId');
			$this->view->form = $form;
		}
		else {
			$this->view->message = __('There is nothing to edit on the Visit Type Sections definition, add diagnosis or procedure beneath it');
		}
		$this->view->ormClasses = $ormClasses;
		$this->view->enumerationId = $enumerationId;
		$this->render();
	}

	public function processEditVisitTypeAction() {
		$enumerationId = (int)$this->_getParam('enumerationId');
		$ormClass = $this->_getParam('ormClass');
		$ormClasses = Visit::ormClasses();
		$data = false;
		if (in_array($ormClass,$ormClasses)) {
			$params = $this->_getParam('visit');
			$diagnosis = new $ormClass();
			$diagnosis->populateWithArray($params);
			$diagnosis->persist();
			if ($enumerationId > 0) {
				$enumeration = new Enumeration();
				$enumeration->enumerationId = $enumerationId;
				$enumeration->populate();
				$enumeration->ormClass = $ormClass;
				$enumeration->ormId = $diagnosis->code;
				$enumeration->persist();
			}
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/* VISIT TYPES SECTION */

	public function visitTypeJsonAction() {
		$rows = array();
		$guid = '9eb793f8-1d5d-4ed5-959d-1e238361e00a';
		$enumeration = new Enumeration();
		$enumeration->populateByGuid($guid);
		$closure = new EnumerationClosure();
		$enumerationIterator = $closure->getAllDescendants($enumeration->enumerationId,1,true);
		foreach ($enumerationIterator as $enum) {
			$row = array();
			$row['id'] = $enum->enumerationId;
			$row['data'] = array();
			$row['data'][] = $enum->name;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows),true);
	}

	public function visitSectionJsonAction() {
		$rows = array();
		$visitType = (int)$this->_getParam('visitType');
		$closure = new EnumerationClosure();
		$modifierId = 0;
		foreach ($closure->getAllDescendants($visitType,1,true) as $enum) {
			if ($enum->key == 'MODIFIERS') {
				$modifierId = $enum->enumerationId;
				continue;
			}
			$enums = array(
				'key'=>array(),
				'value'=>array(),
			);
			foreach ($closure->getAllDescendants($enum->enumerationId,1,true) as $item) {
				switch ($item->ormClass) {
					case 'DiagnosisCodesICD':
					case 'ProcedureCodesCPT':
						break;
					default:
						continue 2;
				}
				$enums['key'][] = $item->key;
				$enums['value'][] = $item->name;
			}
			if (!isset($enums['key'][0])) continue;
			$codes = implode(', ',$enums['key']);
			$row['id'] = $enum->enumerationId;
			$row['data'] = array();
			$row['data'][] = '';
			$row['data'][] = $enum->name.' ('.implode(', ',$enums['key']).')';
			$row['data'][] = implode(', ',$enums['value']);
			$rows[] = $row;
		}
		if ($modifierId > 0 && isset($rows[0])) {
			$modifiers = array();
			foreach ($closure->getAllDescendants($modifierId,1,true) as $item) {
				$modifiers[$item->key] = $item->name;
			}
			$rows[0]['userdata']['modifiers'] = $modifiers;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows),true);
	}

	private function _getVisitSections($visitType) {
		// STUB method?
		$ret = array();
		switch ($visitType) {
			case 'established_patient':
				$data = array();
				$data['id'] = '99211';
				$data['data'][] = '';
				$data['data'][] = 'Brief Exam 1-5Min';
				$data['data'][] = '99211';
				$ret[] = $data;
				$data = array();
				$data['id'] = '99212';
				$data['data'][] = '';
				$data['data'][] = 'Limited Exam 6-10Min';
				$data['data'][] = '99212';
				$ret[] = $data;
				$data = array();
				$data['id'] = '99213';
				$data['data'][] = '';
				$data['data'][] = 'Intermediate Exam 11-19Min';
				$data['data'][] = '99213';
				$ret[] = $data;
				$data = array();
				$data['id'] = '99214';
				$data['data'][] = '';
				$data['data'][] = 'Extended Exam 20-30Min';
				$data['data'][] = '99214';
				$ret[] = $data;
				$data = array();
				$data['id'] = '99215';
				$data['data'][] = '';
				$data['data'][] = 'Comprehensive Exam 31+ Min';
				$data['data'][] = '99215';
				$ret[] = $data;
				break;
		}
		return $ret;
	}

	public function providersJsonAction() {
		$provider = new Provider();
		$providerIterator = $provider->getIter();
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => $providerIterator->toJsonArray('personId',array('displayName'))),true);
	}

	public function currentProvidersJsonAction() {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => array()),true);
	}

	public function visitModifiersJsonAction() {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => array()),true);
	}


	/* VITALS SECTION */

	public function vitalsJsonAction() {
		$rows = array();
		$vitals = new VitalSignGroup();
		$vitalsIter = $vitals->getIterator();
		foreach ($vitalsIter as $vitals) {
			foreach ($vitals->vitalSignValues as $vitalSign) {
				$tmp = array();
				$tmp['id'] = $vitalSign->vitalSignValueId;
				$tmp['data'][] = $vitals->dateTime;
				$tmp['data'][] = $vitalSign->vital;
				$tmp['data'][] = $vitalSign->value; //USS Value
				$tmp['data'][] = 'Metric Value';
				$tmp['data'][] = ''; //Qualifiers
				$tmp['data'][] = $vitals->enteringUserId;
				$rows[] = $tmp;
			}
		}
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => $rows),true);
        }


	/* IMMUNIZATION SECTION */

	public function immunizationsJsonAction() {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => $this->_getImmunization()),true);
        }

	private function _getImmunization() {
		$ret = array();
		$immunizationName = "Sections";
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($immunizationName);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ret = $enumerationIterator->toJsonArray('enumerationId',array('name'));
		return $ret;
	}

	public function immunizationsSectionJsonAction() {
		$immunizations = $this->_getParam('immunizations','');
		$otherPref = 'other_';
		$rows = array();
		if (substr($immunizations,0,strlen($otherPref)) == $otherPref) { // others/immunization
			$immunizations = substr($immunizations,strlen($otherPref));
			$procedureCodesImmunizationIterator = new ProcedureCodesImmunizationIterator();
			$procedureCodesImmunizationIterator->setFilters($immunizations);
			foreach ($procedureCodesImmunizationIterator as $procedure) {
				$tmp = array();
				$tmp['id'] = $otherPref.$procedure->code;
				$tmp['data'][] = '';
				$tmp['data'][] = $procedure->textLong;
				$tmp['data'][] = $procedure->code;
				$rows[] = $tmp;
			}
		}
		else { // enumeration
			$enumeration = new Enumeration();
			$enumeration->enumerationId = (int)$immunizations;
			$enumeration->populate();
			$enumerationsClosure = new EnumerationsClosure();
			$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
			//$rows = $enumerationIterator->toJsonArray('enumerationId',array('name'));
			foreach ($enumerationIterator as $enum) {
				$tmp = array();
				$tmp['id'] = $enum->enumerationId;
				$tmp['data'][] = '';
				$tmp['data'][] = $enum->name;
				$tmp['data'][] = $enum->key;
				$rows[] = $tmp;
			}
		}
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => $rows),true);
        }

	public function immunizationsSeriesJsonAction() {
		$id = (int)$this->_getParam("id");
		$enumeration = new Enumeration();
		$enumeration->enumerationId = (int)$id;
		$enumeration->populate();
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		//$rows = $enumerationIterator->toJsonArray('enumerationId',array('name'));
		$rows = array();
		foreach ($enumerationIterator as $enum) {
			$rows[] = array('id'=>$enum->enumerationId,'data'=>$enum->name);
		}
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows"=>$rows),true);
        }


	/* HEALTH STATUS (HSA) SECTION */

	public function hsaJsonAction() {
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName(HealthStatusAlert::ENUM_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$rows = $enumerationIterator->toJsonArray('enumerationId',array('name'));
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array('rows'=>$rows),true);
        }

	public function hsaSectionJsonAction() {
		$hsa = $this->_getParam('hsa');
		$enumeration = new Enumeration();
		$enumeration->enumerationId = (int)$hsa;
		$enumeration->populate();
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$rows = array();
		foreach ($enumerationIterator as $enum) {
			$tmp = array();
			$tmp['id'] = $enum->enumerationId;
			$tmp['data'][] = '';
			$tmp['data'][] = $enum->name;
			$tmp['data'][] = $enum->key;
			$rows[] = $tmp;
		}
		// temporarily set rows to all defined HSA handlers
		$rows = array();
		$handler = new Handler(Handler::HANDLER_TYPE_HSA);
		$handlerIterator = $handler->getIterator();
		foreach ($handlerIterator as $row) {
			$tmp = array();
			$tmp['id'] = $row->handlerId;
			$tmp['data'][] = '';
			$tmp['data'][] = $row->name;
			$tmp['data'][] = $row->timeframe;
			$rows[] = $tmp;
		}
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array('rows'=>$rows),true);
        }


	/* EXAMS SECTION */

	public function examsJsonAction() {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => array()),true);
        }

	public function examsSectionJsonAction() {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array("rows" => array()),true);
        }


	public function listPatientVisitTypesAction() {
		$patientId = (int)$this->_getParam('patientId');
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();
		if ($patientId > 0) {
			$patientVisitTypeIterator = new PatientVisitTypeIterator();
			$patientVisitTypeIterator->setFilters(array('patientId'=>$patientId,'visitId'=>$visitId));
			foreach ($patientVisitTypeIterator as $visitType) {
				$provider = new Provider();
				$provider->personId = $visitType->providerId;
				$provider->populate();
				$tmp = array();
				$tmp['id'] = $visitType->providerId;
				$tmp['data'][] = $provider->displayName;
				$tmp['data'][] = ($visitType->isPrimary)?__('Primary'):'';
				$rows[] = $tmp;
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processPatientVisitTypesAction() {
		$patientId = (int)$this->_getParam('patientId');
		$visitTypes = $this->_getParam('visitTypes');
		if ($patientId > 0) {
			$patientVisitTypeIterator = new PatientVisitTypeIterator();
			$patientVisitTypeIterator->setFilters(array('patientId'=>$patientId));
			$existingVisitTypes = $patientVisitTypeIterator->toArray('providerId','patientId');
			if (is_array($visitTypes)) {
				foreach ($visitTypes as $providerId=>$visitType) {
					if (isset($existingVisitTypes[$providerId])) {
						unset($existingVisitTypes[$providerId]);
					}
					$visitType['providerId'] = $providerId;
					$visitType['patientId'] = $patientId;
					$patientVisitType = new PatientVisitType();
					$patientVisitType->populateWithArray($visitType);
					$patientVisitType->persist();
				}
			}
			// delete un-used records
			foreach ($existingVisitTypes as $providerId=>$patientId) {
				$patientVisitType = new PatientVisitType();
				$patientVisitType->providerId = $providerId;
				$patientVisitType->patientId = $patientId;
				$patientVisitType->setPersistMode(WebVista_Model_ORM::DELETE);
				$patientVisitType->persist();
			}
		}
		$data = array();
		$data['msg'] = __('Record saved successfully');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processAddVisitTypesAction() {
		$personId = (int)$this->_getParam('personId');
		$visitId = (int)$this->_getParam('visitId');
		$id = (int)$this->_getParam('id');
		$ret = false;
		$closure = new EnumerationClosure();

		$diagnoses = array();
		$procedures = array();
		foreach ($closure->getAllDescendants($id,1,true) as $enum) {
			switch ($enum->ormClass) {
				case 'DiagnosisCodesICD':
					$diagnoses[] = $enum;
					break;
				case 'ProcedureCodesCPT':
					$procedures[] = $enum;
					break;
			}
		}

		$providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		foreach ($procedures as $procedure) {
			$patientProcedure = new PatientProcedure();
			$patientProcedure->code = $procedure->key;
			$patientProcedure->quantity = 1; // default to 1
			$patientProcedure->procedure = $procedure->name;
			$patientProcedure->patientId = $personId;
			$patientProcedure->providerId = $providerId;
			$patientProcedure->visitId = $visitId;
			$diagCtr = 1;
			foreach ($diagnoses as $diagnosis) {
				$key = $diagnosis->key;
				$patientDiagnosis = new PatientDiagnosis();
				$patientDiagnosis->code = $key;
				$patientDiagnosis->dateTime = date('Y-m-d H:i:s');
				$patientDiagnosis->diagnosis = $diagnosis->name;
				$patientDiagnosis->patientId = $personId;
				$patientDiagnosis->providerId = $providerId;
				$patientDiagnosis->visitId = $visitId;
				$patientDiagnosis->persist();

				$diag = 'diagnosisCode'.$diagCtr++;
				$patientProcedure->$diag = $key;
			}
			$patientProcedure->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processAddVisitTypeProvidersAction() {
		$providerIds = $this->_getParam('ids');
		$visitId = (int)$this->_getParam('visitId');
		$personId = (int)$this->_getParam('personId');
		$primary = (int)$this->_getParam('primary');

		$data = false;
		$patientVisitType = new PatientVisitType();
		$patientVisitType->patientId = $personId;
		$patientVisitType->visitId = $visitId;
		foreach (explode(',',$providerIds) as $providerId) {
			$patientVisitType->patientVisitTypeId = 0;
			$patientVisitType->isPrimary = 0;
			if ($providerId == $primary) $patientVisitType->isPrimary = 1;
			$patientVisitType->providerId = (int)$providerId;
			$patientVisitType->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteVisitTypeProvidersAction() {
		$providerIds = $this->_getParam('ids');
		$visitId = (int)$this->_getParam('visitId');
		$personId = (int)$this->_getParam('personId');
		$data = false;
		$patientVisitType = new PatientVisitType();
		$patientVisitType->patientId = $personId;
		$patientVisitType->visitId = $visitId;
		foreach (explode(',',$providerIds) as $providerId) {
			$patientVisitType->patientVisitTypeId = 0;
			$patientVisitType->providerId = (int)$providerId;
			$patientVisitType->populateWithIds();
			if (!$patientVisitType->patientVisitTypeId > 0) continue;
			$patientVisitType->setPersistMode(WebVista_Model_ORM::DELETE);
			$patientVisitType->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSetVisitTypePrimaryProviderAction() {
		$providerId = (int)$this->_getParam('id');
		$visitId = (int)$this->_getParam('visitId');
		$personId = (int)$this->_getParam('personId');
		$isPrimary = (int)$this->_getParam('isPrimary');
		$data = false;
		$patientVisitType = new PatientVisitType();
		$patientVisitType->patientId = $personId;
		$patientVisitType->visitId = $visitId;
		$patientVisitType->providerId = (int)$providerId;
		$patientVisitType->populateWithIds();
		if ($patientVisitType->resetPrimaryProvider()) $data = true;
		$patientVisitType->isPrimary = $isPrimary;
		$patientVisitType->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
