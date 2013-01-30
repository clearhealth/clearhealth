<?php
/*****************************************************************************
*       NsdrManagerController.php
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
 * Name Space Data Resolver Manager controller
 */
class NsdrManagerController extends WebVista_Controller_Action {

	protected $_form = null;
	protected $_nsdrDefinition = null;
	protected $_nsdrDefinitionMethod = null;

	/**
	 * List distinct enumerations names given a enumerationId of category
	 */
	public function listAction() {
		$rows = array();
		$parentId = $this->_getParam('parentId');
		$method = new NSDRDefinitionMethod();
		$methodIterator = $method->getIteratorByParentId($parentId);
		$rows = $methodIterator->toJsonArray('uuid',array('methodName'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function indexAction() {
		//$namespace = "*::com.clearhealth.enumerations.gender";
		//$namespace = "F::com.clearhealth.enumerations.gender";
		//$nsdr = NSDR::populate($namespace);
		//trigger_error(print_r($nsdr,true),E_USER_NOTICE);
		$this->render();
	}

	/**
	 * Add/Edit method
	 */
	public function editMethodAction() {
		$id = $this->_getParam('id');
		$parentId = $this->_getParam('parentId');
		$this->_nsdrDefinitionMethod = new NSDRDefinitionMethod();
		if (strlen($id) > 0) {
			$this->_nsdrDefinitionMethod->uuid = $id;
			$this->_nsdrDefinitionMethod->populate();
		}
		if (strlen($parentId) > 0) {
			$this->_nsdrDefinitionMethod->nsdrDefinitionUuid = $this->_getParam('parentId');
		}
		$this->_form = new WebVista_Form(array('name' => 'nsdrDefinitionMethod'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'nsdr-manager.raw/process-edit-method');
		$this->_form->loadORM($this->_nsdrDefinitionMethod,'nsdrDefinitionMethod');
		$this->_form->setWindow('windowEditMethodId');
		$this->view->form = $this->_form;
		$this->render('edit-method');
	}

	/**
	 * Process the modified NSDR method
	 */
	public function processEditMethodAction() {
		$params = $this->_getParam('nsdrDefinitionMethod');
		$nsdrDefinitionMethod = new NSDRDefinitionMethod();
		$nsdrDefinitionMethod->populateWithArray($params);
		$nsdrDefinitionMethod->methodName = NSDRDefinitionMethod::normalizeMethodName($nsdrDefinitionMethod->methodName);
		$validCode = NSDRDefinitionMethod::isPHPCodeValid($nsdrDefinitionMethod->method,$nsdrDefinitionMethod->methodName);
		// check for method duplicates
		if ($nsdrDefinitionMethod->isMethodNameExists()) {
			$data = 'Method name "'.$nsdrDefinitionMethod->methodName.'" already exists.';
		}
		else if ($validCode !== true) {
			$data = $validCode;
		}
		else {
			$nsdrDefinitionMethod->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Outputs xml left side toolbar menu
	 */
	public function leftToolbarXmlAction() {
		$strXml = '<?xml version="1.0" encoding="iso-8859-1"?>
		<toolbar>
		<item id="add" type="button" title="Add" img="newproblem.png" imgdis="newproblem.png"/>
		<item id="delete" type="button" title="Delete" img="removeproblem.png" imgdis="removeproblem.png"/>
		<item id="sep1" type="separator"/>
		<item id="start" type="button" title="Start" img="start.png" imgdis="start.png"/>
		<item id="reload" type="button" title="Reload" img="reload.png" imgdis="reload.png"/>
		<item id="status" type="button" title="Status" img="getStatus.png" imgdis="getStatus.png"/>
		</toolbar>';
		$this->view->toolbarXml = $strXml;
		header("Content-type: text/xml");
		$this->render();
	}

	public function dataPointsXmlAction() {
		$parentId = $_GET["id"];
		if (!strlen($parentId) > 0) {
			$parentId = 0;
		}
		$filter = array();
		$filter['id'] = $parentId;
		$nsdrDefinitionIterator = new NSDRDefinitionIterator();
		$nsdrDefinitionIterator->setFilters($filter);
		$xml = "<tree id='".$parentId."'>";
		foreach ($nsdrDefinitionIterator as $nsdrIterator) {
			$text = $nsdrIterator->namespace;
			// check if parentId is the prefix of the current namespace
			if (strpos($text,$parentId) === 0) {
				// strip the prefix for text
				$text = substr($text,strlen($parentId)+1);
			}
			$xml .= "<item child='1' id='".$nsdrIterator->namespace."' text='".$text."'><userdata name='uuid'>".$nsdrIterator->uuid."</userdata></item>";
		}
		$xml .= "</tree>";
		$this->view->xml = $xml;
		header("Content-type: text/xml");
		$this->render();
	}

	/**
	 * Delete NSDR definition action, this must be called using AJAX
	 */
	public function ajaxDeleteAction() {
		$uuid = $this->_getParam('uuid');
		$this->_nsdrDefinition = new NSDRDefinition();
		$this->_nsdrDefinition->uuid = $uuid;
		$this->_nsdrDefinition->setPersistMode(WebVista_Model_ORM::DELETE);
		$this->_nsdrDefinition->persist();
		$msg = __("Record deleted successfully");
		$data = array();
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Edit NSDR definition action
	 */
	public function editAction() {
		$id = $this->_getParam('uuid');
		$event = $this->_getParam('event');
		$parentId = $this->_getParam('parentId');
		$this->_nsdrDefinition = new NSDRDefinition();
		if (strlen($id) > 0) {
			$this->_nsdrDefinition->uuid = $id;
			$this->_nsdrDefinition->populate();
		}
		if ($event == 'add') {
			$tmpObjNSDR = new NSDRDefinition();
			if (!strlen($this->_nsdrDefinition->namespace) > 0) {
				$this->_nsdrDefinition->namespace = $parentId;
			}
			$tmpObjNSDR->namespace = $this->_nsdrDefinition->namespace . '.';
			$this->_nsdrDefinition = $tmpObjNSDR;
		}
		$this->view->event = $event;
		$this->view->parentId = $parentId;
		$this->_form = new WebVista_Form(array('name' => 'nsdrDefinition'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'nsdr-manager.raw/process-edit');
		$this->_form->loadORM($this->_nsdrDefinition,'nsdrDefinition');
		$this->_form->setWindow('windowEditNSDRId');
		$this->view->form = $this->_form;

		$this->view->noMethod = false;
		if (!strlen($this->_nsdrDefinition->uuid) > 0 || strlen($this->_nsdrDefinition->aliasFor) > 0 || strlen($this->_nsdrDefinition->ORMClass) > 0) {
			$this->view->noMethod = true;
		}
		$this->render('edit');
	}

	/**
	 * Process the modified NSDR definition together its NSDR definition methods
	 */
	public function processEditAction() {
		$this->editAction();

		// NSDR Definition parameters
		$params = $this->_getParam('nsdrDefinition');
		$this->_nsdrDefinition->populateWithArray($params);
		$id = $params['uuid'];
		if (!strlen($id) > 0) {
			$this->_nsdrDefinition->uuid = NSDR::create_guid();
		}

		$message = __('Record Saved for NSDR Definition: ' . $this->_nsdrDefinition->namespace);
		$code = 200;
		// cannot add method if alias exists (alias must be canonical)
		if (strlen($this->_nsdrDefinition->aliasFor) > 0) {
			$nsdr = new NSDRDefinition();
			//$nsdr->uuid = $this->_nsdrDefinition->aliasFor;
			//$nsdr->populate();
			$nsdr->populateByNamespace($this->_nsdrDefinition->aliasFor);
			if (strlen($nsdr->namespace) > 0) {
				if (strlen($nsdr->aliasFor) > 0) {
					$this->_nsdrDefinition->aliasFor = '';
					$message = __('Alias must be canonical.');
					$code = 400;
				}
			}
			else {
				$this->_nsdrDefinition->aliasFor = '';
				$message = __('Alias does not exists.');
				$code = 401;
			}
		}

		if (strlen($this->_nsdrDefinition->ORMClass) > 0) {
			if (!NSDRDefinition::isORMClassImplementsMethod($this->_nsdrDefinition->ORMClass)) {
				$this->_nsdrDefinition->ORMClass = '';
				$message = __('Invalid ORM Class');
				$code = 402;
			}
		}

		$this->_nsdrDefinition->persist();
		$this->view->message = $message;
		$this->view->code = $code;
		$this->render('edit');
	}

	/**
	 * Process NSDR Method removal
	 */
	public function processRemoveMethodAction() {
		$uuid = $this->_getParam('uuid');
		$this->_nsdrDefinitionMethod = new NSDRDefinitionMethod();
		$this->_nsdrDefinitionMethod->uuid = $uuid;
		$this->_nsdrDefinitionMethod->setPersistMode(WebVista_Model_ORM::DELETE);
		$this->_nsdrDefinitionMethod->persist();
		$msg = __("Record deleted successfully");
		$data = array();
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
