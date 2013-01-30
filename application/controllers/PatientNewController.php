<?php
/*****************************************************************************
*       PatientNewController.php
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


class PatientNewController extends WebVista_Controller_Action {

	protected $_session;
	protected $_patient;
	protected $_form;
	protected $_location;

	public function init() {
		//$this->_location = $cprs->location;
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
        	if (isset($this->_session->messages)) {
        	    $this->view->messages = $this->_session->messages;
        	}
		$this->_form = new WebVista_Form(array('name' => 'patient-new'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "patient-new.raw/add-process");
		$this->_patient = new Patient();
		$this->_patient->defaultProvider = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$this->_address = new Address();
		$this->_phoneNumber = new PhoneNumber();
		$this->_form->loadORM($this->_patient, "Patient");
		$this->_form->setWindow('windowNewPatientId');
		//$this->_form->registrationLocationId->setValue($this->_location->locationId);
		$this->view->form = $this->_form;

		$this->view->statesList = Address::getStatesList();
		$this->view->phoneTypes = PhoneNumber::getListPhoneTypes();
		$this->view->addressTypes = Address::getListAddressTypes();
        	$this->render();
	}

	public function addProcessAction() {
		//$this->indexAction();
		$params = $this->_getParam('patient');
		$patient = new Patient();
		//$this->_form->isValid($_POST);
		$patient->populateWithArray($params);
		$duplicates = Person::checkDuplicatePerson($patient->person);
		$ret = array();
		if (!isset($duplicates[0])) {
			//$patient->persist();
			return $this->processNewPatientAction();
			$ret['msg'] = "Record Saved for Patient: " . ucfirst($patient->firstName) . " " . ucfirst($patient->lastName);
			$ret['personId'] = $patient->personId;
		}
		else {
			$ret['duplicates'] = $duplicates;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processNewPatientAction() {
		$params = $this->_getParam('patient');
		$patient = new Patient();
		$patient->populateWithArray($params);
		if (!strlen($patient->recordNumber) > 0) {
			$patient->recordNumber = WebVista_Model_ORM::nextSequenceId('record_sequence');
		}
		$patient->persist();
		$personId = (int)$patient->personId;
		// save addresses and phones
		$addresses = $this->_getParam('addresses');
		if (is_array($addresses)) {
			foreach ($addresses as $row) {
				$address = new Address();
				$address->populateWithArray($row);
				$address->personId = $personId;
				$address->persist();
			}
		}
		$phones = $this->_getParam('phones');
		if (is_array($phones)) {
			foreach ($phones as $row) {
				$phone = new PhoneNumber();
				$phone->populateWithArray($row);
				$phone->personId = $personId;
				$phone->persist();
			}
		}
		$ret = array();
		$ret['msg'] = 'Record Saved for Patient: '.ucfirst($patient->firstName).' '.ucfirst($patient->lastName);
		$ret['personId'] = $personId;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render();
	}

}
