<?php
/*****************************************************************************
*       PostingsController.php
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
 * Posting controller
 */
class PostingsController extends WebVista_Controller_Action {

	protected $_patientNote = null;

	public function indexAction() {
		// temporarily redirect request to patient action
		$this->patientAction();
	}

	public function patientAction() {
		$personId = (int)$this->_getParam('personId');
		$this->render('patient');
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

	public function listAction() {
		$showAll = (int)$this->_getParam('showAll');
		$patientId = (int)$this->_getParam('patientId');
		$rows = array();
		$patientNote = new PatientNote();
		$patientNoteIterator = $patientNote->getIterator();
		$filters = array();
		$filters['patient_id'] = $patientId;
		$filters['posting'] = 1;
		if (!$showAll) {
			$filters['active'] = 1;
		}
		$patientNoteIterator->setFilters($filters);
		foreach ($patientNoteIterator as $note) {
			$tmp = array();
			$tmp['id'] = $note->patientNoteId;
			$tmp['data'][] = $note->priority;
			$tmp['data'][] = $note->noteDate;
			$tmp['data'][] = $note->user->username;
			$tmp['data'][] = $note->reason;
			$tmp['data'][] = $note->note;
			$tmp['data'][] = $note->active;
			$tmp['data'][] = $note->active;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function addAction() {
		$this->_patientNote = new PatientAllergy();
		$this->_patientNote->patientId = (int)$this->_getParam('personId');
		$this->_patientNote->causativeAgent = $this->_getParam('allergy','');

		$this->_form = new WebVista_Form(array('name'=>'add'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'allergies.raw/process-add');
		$this->_form->loadORM($this->_patientNote,'allergy');
		$this->_form->setWindow('winAddAllergyId');
		$this->view->form = $this->_form;

		$patientNoteIterator = $this->_patientNote->getIteratorByPatient();
		$ctr = 0;
		foreach ($patientNoteIterator as $allergy) {
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

	public function processEditAction() {
		$this->addAction();
		$params = $this->_getParam('allergy');
		$params['symptoms'] = implode(',',$params['symptoms']);
		$params['dateTimeCreated'] = date('Y-m-d H:i:s');
		$this->_patientNote->populateWithArray($params);
		$this->_patientNote->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('add');
	}

}
