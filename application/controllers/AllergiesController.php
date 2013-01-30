<?php
/*****************************************************************************
*       AllergiesController.php
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
 * Allergy controller
 */
class AllergiesController extends WebVista_Controller_Action {

	protected $_patientAllergy = null;
	protected $_form = null;
	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		// temporarily redirect request to patient action
		$this->patientAction();
	}

	public function patientAction() {
		$personId = (int)$this->_getParam('personId');
		$this->view->reasons = PatientNote::listReasons();
		if (!isset($this->_session->active)) {
			$this->_session->active = 1;
		}
		if (!isset($this->_session->inactive)) {
			$this->_session->inactive = 1;
		}
		$this->view->active = $this->_session->active;
		$this->view->inactive = $this->_session->inactive;
		$this->render('patient');
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

	public function detailsAction() {
		$id = (int)$this->_getParam('id');
		$this->_patientAllergy = new PatientAllergy();
		$this->_patientAllergy->patientAllergyId = $id;
		$this->_patientAllergy->populate();

		$this->_form = new WebVista_Form(array('name'=>'add'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'allergies.raw/process-add');
		$this->_form->loadORM($this->_patientAllergy,'allergy');
		$this->_form->setWindow('winDetailsAllergyId');
		$this->view->form = $this->_form;

		$this->render('details');
	}

	public function listAction() {
		$personId = (int)$this->_getParam('personId');
		$showActive = (int)$this->_getParam('active');
		$showInactive = (int)$this->_getParam('inactive');
		$this->_session->active = $showActive;
		$this->_session->inactive = $showInactive;

		$enumeration = new Enumeration();
		$listSeverities = array();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SEVERITY_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listSeverities[$enum->key] = $enum->name;
		}

		$listSymptoms = array();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SYMPTOM_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listSymptoms[$enum->key] = $enum->name;
		}

		$active = null;
		if ($showActive && !$showInactive) {
			$active = 1;
		}
		else if ($showInactive && !$showActive) {
			$active = 0;
		}
		else if (!$showInactive && !$showActive) {
			$active = 2; // invalid so no items will be retrieved
		}
		$patientAllergy = new PatientAllergy();
		$patientAllergy->patientId = $personId;
		$rows = array(array('id'=>'ctrRowId','data'=>array('','','','',''),'userdata'=>array('noOfAllergies'=>PatientAllergy::countAllergiesByPatientId($personId)))); // added extra row, this will hide
		if ($patientAllergy->populateByNoKnowAllergies()) {
			$rows[] = array('id'=>$patientAllergy->patientAllergyId,'data'=>array(__('No Known Allergies')));
		}
		else {
			$patientAllergyIterator = $patientAllergy->getIteratorByPatient($personId,0,null,$active);
			foreach ($patientAllergyIterator as $allergy) {
				$severity = isset($listSeverities[$allergy->severity])?$listSeverities[$allergy->severity]:'';
				$exp = explode(',',$allergy->symptoms);
				$symptoms = array();
				foreach ($exp as $symp) {
					$symptoms[] = isset($listSymptoms[$symp])?$listSymptoms[$symp]:'';
				}
				$tmp = array();
				$tmp['id'] = $allergy->patientAllergyId;
				$tmp['data'][] = $allergy->causativeAgent;
				$tmp['data'][] = $severity;
				$tmp['data'][] = implode(', ',$symptoms);
				$tmp['data'][] = $allergy->comments;
				$tmp['userdata']['active'] = (int)$allergy->active;
				$rows[] = $tmp;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function lookupAction() {
		$personId = (int)$this->_getParam('personId');
		$this->view->jsCallback = $this->_getParam('callback');
		$this->render('lookup');
	}

	public function processLookupAction() {
		$q = preg_replace('/[^a-zA-Z0-9\%\.]/','',$this->_getParam('q',''));
		$rows = array();
		if (strlen($q) > 0) {
			$ctr = 1;

			$allergyIterator = new DiagnosisCodesAllergyIterator();
			$allergyIterator->setFilters($q);
			$allergyRows = $allergyIterator->toJsonArray('code',array('textShort'));
			if (count($allergyRows) > 0) {
				$tmp = array();
				$tmp['id'] = $ctr++;
				$tmp['data'][] = __('Allergy File');
				$tmp['rows'] = $allergyRows;
				$rows[] = $tmp;
			}

			$drugAllergyIterator = new BaseMed24DrugAllergyIterator();
			$drugAllergyIterator->setFilters(array('formulary' => 'default', 'value' => $q));
			$drugAllergyRows = $drugAllergyIterator->toJsonArray('vaclass',array('notice'));
			if (count($drugAllergyRows)) {
				$tmp = array();
				$tmp['id'] = $ctr++;
				$tmp['data'][] = __('Drug Class');
				$tmp['rows'] = $drugAllergyRows;
				$rows[] = $tmp;
			}

			$specificDrugAllergyIterator = new BaseMed24SpecificDrugAllergyIterator();
			$specificDrugAllergyIterator->setFilters($q);
			$specificDrugAllergyRows = $specificDrugAllergyIterator->toJsonArray('md5',array('notice'));
			if (count($specificDrugAllergyRows)) {
				$tmp = array();
				$tmp['id'] = $ctr++;
				$tmp['data'][] = __('Specific Drug');
				$tmp['rows'] = $specificDrugAllergyRows;
				$rows[] = $tmp;
			}

			$freeRows = array();
			$tmp = array();
			$tmp['id'] = $ctr++;
			$tmp['data'][] = $q;
			$freeRows[] = $tmp;

			$tmp = array();
			$tmp['id'] = $ctr++;
			$tmp['data'][] = __('Add new free-text allergy');
			$tmp['rows'] = $freeRows;
			$rows[] = $tmp;
			
		}

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function addAction() {
		$this->_patientAllergy = new PatientAllergy();
		$this->_patientAllergy->patientId = (int)$this->_getParam('personId');
		$this->_patientAllergy->causativeAgent = $this->_getParam('allergy','');
		$this->_edit();
	}

	public function editAction() {
		$this->_patientAllergy = new PatientAllergy();
		$this->_patientAllergy->patientAllergyId = (int)$this->_getParam('id');
		$this->_patientAllergy->populate();
		$this->_edit();
	}

	protected function _edit() {
		$this->_form = new WebVista_Form(array('name'=>'add'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'allergies.raw/process-add');
		$this->_form->loadORM($this->_patientAllergy,'allergy');
		$this->_form->setWindow('winAddAllergyId');
		$this->view->form = $this->_form;

		$patientAllergyIterator = $this->_patientAllergy->getIteratorByPatient();
		$ctr = 0;
		foreach ($patientAllergyIterator as $allergy) {
			$ctr++;
		}
		$this->view->disableNoKnownAllergies = ($ctr > 0)?true:false;

		$listReactionTypes = array(''=>'');
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_REACTION_TYPE_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listReactionTypes[$enum->key] = $enum->name;
		}
		$this->view->listReactionTypes = $listReactionTypes;

		$listSeverities = array(''=>'');
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SEVERITY_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listSeverities[$enum->key] = $enum->name;
		}
		$this->view->listSeverities = $listSeverities;

		$listSymptoms = array();
		$enumeration->populateByEnumerationName(PatientAllergy::ENUM_SYMPTOM_PARENT_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ctr = 0;
		foreach ($enumerationIterator as $enum) {
			$listSymptoms[$enum->key] = $enum->name;
		}
		$this->view->listSymptoms = $listSymptoms;

		$listObservers = array(''=>'');
		$providerIterator = new ProviderIterator();
		foreach ($providerIterator as $provider) {
			$listObservers[$provider->personId] = $provider->displayName;
		}
		$this->view->listObservers = $listObservers;

		$this->render('add');
	}

	public function processAddAction() {
		$this->addAction();
		$params = $this->_getParam('allergy');
		$params['symptoms'] = implode(',',$params['symptoms']);
		$params['dateTimeCreated'] = date('Y-m-d H:i:s');

		if (!isset($params['patientAllergyId']) || !(int)$params['patientAllergyId'] > 0) {
			$patientAllergy = new PatientAllergy();
			$patientAllergy->patientId = (int)$params['patientId'];
			if ($patientAllergy->populateByNoKnowAllergies()) {
				$this->_patientAllergy = $patientAllergy;
				$this->_patientAllergy->noKnownAllergies = 0;
				if (isset($params['patientAllergyId'])) unset($params['patientAllergyId']);
			}
		}

		$this->_patientAllergy->populateWithArray($params);
		if (!$this->_patientAllergy->patientAllergyId > 0) $this->_patientAllergy->active = 1; // temporarily set to active for new allergy
		$this->_patientAllergy->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('add');
	}

	public function processMarkNoKnownAllergiesAction() {
		$personId = (int)$this->_getParam('personId');
		$patientAllergy = new PatientAllergy();
		$patientAllergy->patientId = $personId;
		$patientAllergyIterator = $patientAllergy->getIteratorByPatient($personId,0,1);
		foreach ($patientAllergyIterator as $allergy) {
			if ($allergy->noKnownAllergies) {
				$patientAllergy->patientAllergyId = $allergy->patientAllergyId;
				break;
			}
		}
		$patientAllergy->noKnownAllergies = 1;
		$patientAllergy->dateTimeCreated = date('Y-m-d H:i:s');
		$patientAllergy->persist();
		$data = __('Mark as no known allergies successful');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processMarkEnteredInErrorAction() {
		$id = (int)$this->_getParam('id');
		$patientAllergy = new PatientAllergy();
		$patientAllergy->patientAllergyId = $id;
		$patientAllergy->populate();
		if ($patientAllergy->patientId > 0) {
			$patientAllergy->enteredInError = 1;
			$patientAllergy->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function processMarkActiveAction() {
		$id = (int)$this->_getParam('id');
		$active = (int)$this->_getParam('active');
		$patientAllergy = new PatientAllergy();
		$patientAllergy->patientAllergyId = $id;
		$ret = false;
		if ($patientAllergy->populate()) {
			$patientAllergy->active = $active;
			$patientAllergy->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

}
