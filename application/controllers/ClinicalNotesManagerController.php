<?php
/*****************************************************************************
*       ClinicalNotesManagerController.php
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
 * Clinical Notes Manager controller
 */
class ClinicalNotesManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	/**
	 * Delete clinical notes template action
	 */
	public function deleteTemplateAction() {
		$clinicalNoteTemplateId = (int)$this->_getParam('clinicalNoteTemplateId');
		$cnTemplate = new ClinicalNoteTemplate();
		$cnTemplate->clinicalNoteTemplateId = $clinicalNoteTemplateId;
		$cnTemplate->setPersistMode(WebVista_Model_ORM::DELETE);
		$cnTemplate->persist();
		$msg = __("Record deleted successfully");
		$data = array();
		$data['code'] = 200;
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Delete clinical notes definition action
	 */
	public function deleteDefinitionAction() {
		$clinicalNoteDefinitionId = (int)$this->_getParam('clinicalNoteDefinitionId');
		$cnDefinition = new ClinicalNoteDefinition();
		$cnDefinition->clinicalNoteDefinitionId = $clinicalNoteDefinitionId;
		$cnDefinition->setPersistMode(WebVista_Model_ORM::DELETE);
		$cnDefinition->persist();
		$msg = __("Record deleted successfully");
		$data = array();
		$data['code'] = 200;
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Edit clinical notes template, this must be called using AJAX
	 */
	public function editTemplateAction() {
		$clinicalNoteTemplateId = (int)$this->_getParam('clinicalNoteTemplateId');
		$cnTemplate = new ClinicalNoteTemplate();
		if ($clinicalNoteTemplateId > 0) {
			$cnTemplate->clinicalNoteTemplateId = $clinicalNoteTemplateId;
			$cnTemplate->populate();
			$definitions = array();
			$cndIterator = new ClinicalNoteDefinitionIterator();
			$filters = array();
			$filters['clinicalNoteTemplateId'] = $clinicalNoteTemplateId;
			$cndIterator->setFilters($filters);
			$this->view->definitions = $cndIterator;
		}
		$objForm = new WebVista_Form(array('name' => 'cnTemplate'));
		$objForm->setAction(Zend_Registry::get('baseUrl') . "clinical-notes-manager.raw/process-edit-template");
		$objForm->loadORM($cnTemplate,"cnTemplate");
		$objForm->setWindow('windowEditTemplate');
		$this->view->form = $objForm;
		$this->render();
	}

	public function checkNamespacesAction() {
		$params = $this->_getParam('cnTemplate');
		$cnTemplate = new ClinicalNoteTemplate();
		$cnTemplate->populateWithArray($params);

		$data = array();
		try {
			$xml = new SimpleXMLElement($cnTemplate->template);
		}
		catch (Exception $e) {
			$data['error'] = $e->getMessage();
		}
		if (!isset($data['error']) && (string)$xml->attributes()->useNSDR && (string)$xml->attributes()->useNSDR == 'true') {
			$namespaceAdd = false;
			$namespaces = array();
			$nsdrDefinition = new NSDRDefinition();
			foreach ($xml as $questions) {
				foreach($questions as $key=>$item) {
					$namespace = (string)$item->attributes()->namespace;
					if ($key != 'dataPoint' || ($namespace && !strlen($namespace) > 0)) {
						continue;
					}
					// extract namespace only
					$namespace = NSDR2::extractNamespace($namespace);
					// check if namespace exists then auto-add if does not
					if (!$nsdrDefinition->isNamespaceExists($namespace)) {
						$namespaces[] = $namespace;
					}
				}
			}
			$data['namespaces'] = $namespaces;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Process the modified clinical notes template
	 */
	public function processEditTemplateAction() {
		$params = $this->_getParam('cnTemplate');
		$autoAdd = (int)$this->_getParam('autoAdd');
		$cnTemplate = new ClinicalNoteTemplate();
		$cnTemplate->populateWithArray($params);
		$data = array();
		try {
			$xml = new SimpleXMLElement($cnTemplate->template);
			$data['msg'] = __('Record saved successfully.');
			$cnTemplate->persist();
		}
		catch (Exception $e) {
			$data['error'] = __('Error: '.$e->getMessage());
		}
		if (!isset($data['error']) && (string)$xml->attributes()->useNSDR && (string)$xml->attributes()->useNSDR == 'true') {
			$namespaceAdd = false;
			$namespaces = array();
			$nsdrDefinition = new NSDRDefinition();
			foreach ($xml as $questions) {
				foreach($questions as $key=>$item) {
					$namespace = (string)$item->attributes()->namespace;
					if ($key != 'dataPoint' || ($namespace && !strlen($namespace) > 0)) {
						continue;
					}
					// extract namespace only
					$namespace = NSDR2::extractNamespace($namespace);
					// check if namespace exists then auto-add if does not
					if (!$nsdrDefinition->isNamespaceExists($namespace) && $autoAdd) {
						$nsdrDefinition->addNamespace($namespace,'GenericData');
						$namespaceAdd = true;
					}
				}
			}
			if ($namespaceAdd) {
				$data['msg'] .= "\n\n".__('Please reload NSDR');
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Edit clinical notes definition, this must be called using AJAX
	 */
	public function editDefinitionAction() {
		$clinicalNoteDefinitionId = (int)$this->_getParam('clinicalNoteDefinitionId');
		$clinicalNoteTemplateId = (int)$this->_getParam('clinicalNoteTemplateId');
		$cnDefinition = new ClinicalNoteDefinition();
		$cnDefinition->clinicalNoteTemplateId = $clinicalNoteTemplateId;
		if ($clinicalNoteDefinitionId > 0) {
			$cnDefinition->clinicalNoteDefinitionId = $clinicalNoteDefinitionId;
			$cnDefinition->populate();
		}
		$objForm = new WebVista_Form(array('name' => 'cnDefinition'));
		$objForm->setAction(Zend_Registry::get('baseUrl') . "clinical-notes-manager.raw/process-edit-definition");
		$objForm->loadORM($cnDefinition,"cnDefinition");
		$objForm->setWindow('windowEditDefinition');
		$this->view->form = $objForm;
		$this->render();
	}

	/**
	 * Process the modified clinical notes definition, this must be called using AJAX
	 */
	public function processEditDefinitionAction() {
		$params = $this->_getParam('cnDefinition');
		if (isset($params['active'])) {
			$params['active'] = 1;
		}
		else {
			$params['active'] = 0;
		}
		$cnDefinition = new ClinicalNoteDefinition();
		$cnDefinition->populateWithArray($params);
		$cnDefinition->persist();
		$data = array();
		$data['code'] = 200;
		$data['clinicalNoteDefinitionId'] = $cnDefinition->clinicalNoteDefinitionId;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Outputs xml left side toolbar menu
	 */
	public function leftToolbarXmlAction() {
		header("Content-type: text/xml");
		$this->render();
	}

	/**
	 * List of templates in JSON format
	 */
	public function templatesJsonAction() {
		$rows = array();
		$cntIterator = new ClinicalNoteTemplateIterator();
		foreach ($cntIterator as $cnTemplate) {
			$tmp = array();
			$tmp['id'] = $cnTemplate->clinicalNoteTemplateId;
			$tmp['data'][] = $cnTemplate->name;
			$rows[] = $tmp;
		}

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * List of definitions in JSON format
	 */
	public function definitionsJsonAction() {
		$clinicalNoteTemplateId = (int)$this->_getParam('clinicalNoteTemplateId');
		$rows = array();
		$cndIterator = new ClinicalNoteDefinitionIterator();
		$filters = array();
		$filters['clinicalNoteTemplateId'] = $clinicalNoteTemplateId;
		$cndIterator->setFilters($filters);
		foreach ($cndIterator as $cnDefinition) {
			$tmp = array();
			$tmp['id'] = $cnDefinition->clinicalNoteDefinitionId;
			$tmp['data'][] = $cnDefinition->title;
			$rows[] = $tmp;
		}

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}
}
