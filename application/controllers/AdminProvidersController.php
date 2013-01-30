<?php
/*****************************************************************************
*       AdminProvidersController.php
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


class AdminProvidersController extends WebVista_Controller_Action
{
	protected $_form;
	protected $_provider;
	
    public function indexAction() {
        $this->render();
    }

	public function editAction() {
		$personId = (int)$this->_getParam('personId');
		if (isset($this->_session->messages)) {
			$this->view->messages = $this->_session->messages;
		}
		$this->_form = new WebVista_Form(array('name' => 'provider-detail'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "admin-providers.raw/edit-process");
		$this->_provider = new Provider();
		$this->_provider->person_id = $personId;
		if (!$this->_provider->populate()) {
			if ($personId > 0) {
				//handle case where person exists but no provider record
				$this->view->noProvider = true;
			}
			//do nothing if personId is 0, no person selected yet
		}
		$this->_form->loadORM($this->_provider,'provider');
		//var_dump($this->_form);
		$this->view->form = $this->_form;
		$this->view->person = $this->_provider;

		$stations = Enumeration::getEnumArray(Routing::ENUM_PARENT_NAME);
		$stations = array_merge(array('' => ''),$stations);
		$this->view->stations = $stations;

		$specialties = array(''=>'');
		$listSpecialties = Provider::getListSpecialties();
		 // temporarily use AM = American Medical Association
		foreach ($listSpecialties['AM'] as $specialty) {
			$specialties[$specialty['code']] = $specialty['description'];
		}
		$this->view->specialties = $specialties;
		$this->view->colors = Room::getColorList();

		$this->render('edit');
	}

	function editProcessAction() {
		$params = $this->_getParam('provider');
		$personId = (int)$params['personId'];
		$person = null;
		if (isset($params['provider']['person']) && is_array($params['provider']['person'])) {
			$person = $params['provider']['person'];
			unset($params['provider']['person']);
		}
		$this->_provider = new Provider();
		if ($personId > 0) {
			$this->_provider->personId = $personId;
			$this->_provider->populate();
		}
		$this->_provider->populateWithArray($params);
		// disable cascade persist due to Provider::setProviderId()
		$this->_provider->person->_cascadePersist = false;
		$this->_provider->persist();
		if ($person !== null) {
			// need to separate the persist calls for person due to Provider::setProviderId() method which reinitialize the Person object right after populating
			$this->_provider->person->populateWithArray($person);
			$this->_provider->person->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$msg = "Record Saved for Provider: " . ucfirst($this->_provider->firstName) . " " . ucfirst($this->_provider->lastName);
                $json->direct($msg);
	}

	public function addProcessAction() {
		$personId = (int)$this->_getParam('personId');
		$this->_provider = new Provider();
		$this->_provider->person_id = $personId;
		$this->_provider->populate();
		$this->_provider->persist();
		$acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$acj->suppressExit = true;
		$acj->direct(array(true));
	}

}
