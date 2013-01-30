<?php
/*****************************************************************************
*       DataIntegrationController.php
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


class DataIntegrationController extends WebVista_Controller_Action {

	protected $_datasource = null;
	protected $_template = null;
	protected $_action = null;
	protected $_destination = null;

	public function listDatasourcesAction() {
		$type = (int)$this->_getParam('type');
		$datasource = new DataIntegrationDatasource($type);
		$datasourceIterator = $datasource->getIterator();
		$rows = $datasourceIterator->toJsonArray('dataIntegrationDatasourceId',array('name'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listTemplatesAction() {
		$type = (int)$this->_getParam('type');
		$template = new DataIntegrationTemplate($type);
		$templateIterator = $template->getIterator();
		$rows = $templateIterator->toJsonArray('dataIntegrationTemplateId',array('name'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listDestinationsAction() {
		$type = (int)$this->_getParam('type');
		$destination = new DataIntegrationDestination($type);
		$destinationIterator = $destination->getIterator();
		$rows = $destinationIterator->toJsonArray('dataIntegrationDestinationId',array('name'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listActionsAction() {
		$action = new DataIntegrationAction();
		$actionIterator = $action->getIterator();
		$rows = $actionIterator->toJsonArray('dataIntegrationActionId',array('name'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	/**
	 * Process datasource copy
	 */
	public function processCopyDatasourceAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$data = array();
		$data['msg'] = __('ID '.$id.' is invalid');
		if ($id > 0) {
			$datasourceSrc = new DataIntegrationDatasource($type);
			$datasourceSrc->dataIntegrationDatasourceId = $id;
			$datasourceSrc->populate();

			$datasourceDst = new DataIntegrationDatasource($type);
			$datasourceDst->populateWithArray($datasourceSrc->toArray());
			$datasourceDst->name = 'Copy of '.$datasourceDst->name;
			$datasourceDst->dataIntegrationDatasourceId = 0;
			$datasourceDst->handlerType = $type;
			$datasourceDst->persist();
			$data['msg'] = __('Updated successfully');
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Render edit data source
	 */
	public function editDatasourceAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$this->_datasource = new DataIntegrationDatasource($type);
		if ($id > 0) {
			$this->_datasource->dataIntegrationDatasourceId= $id;
			$this->_datasource->populate();
		}
		$this->_datasource->handlerType = $type;
		$this->_form = new WebVista_Form(array('name'=>'edit-datasource'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'data-integration.raw/process-edit-datasource');
		$this->_form->loadORM($this->_datasource,'datasource');
		$this->_form->setWindow('winEditDataIntegrationDatasourceId');
		$this->view->form = $this->_form;
		$this->view->datasource = $this->_datasource;
		$this->view->callback = $this->_getParam('callback','');
		$this->render('edit-datasource');
	}

	/**
	 * Process modifications on datasource edit
	 */
	public function processEditDatasourceAction() {
		$this->editDatasourceAction();
		$params = $this->_getParam('datasource');
		$this->_datasource->populateWithArray($params);
		$this->_datasource->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-datasource');
	}

	/**
	 * Render edit template
	 */
	public function editTemplateAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$this->_template = new DataIntegrationTemplate();
		if ($id > 0) {
			$this->_template->dataIntegrationTemplateId = $id;
			$this->_template->populate();
		}
		$this->_template->handlerType = $type;
		$this->_form = new WebVista_Form(array('name'=>'edit-template'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'data-integration.raw/process-edit-template');
		$this->_form->loadORM($this->_template,'template');
		$this->_form->setWindow('winEditDataIntegrationTemplateId');
		$this->view->form = $this->_form;
		$this->view->callback = $this->_getParam('callback','');
		$this->render('edit-template');
	}

	/**
	 * Process modifications on template edit
	 */
	public function processEditTemplateAction() {
		$this->editTemplateAction();
		$params = $this->_getParam('template');
		$this->_template->populateWithArray($params);
		$this->_template->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-template');
	}

	/**
	 * Render edit action
	 */
	public function editActionAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$this->_action = new DataIntegrationAction();
		if ($id > 0) {
			$this->_action->dataIntegrationActionId = $id;
			$this->_action->populate();
		}
		$this->_action->handlerType = $type;
		$this->_form = new WebVista_Form(array('name'=>'edit-action'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'data-integration.raw/process-edit-action');
		$this->_form->loadORM($this->_action,'dataIntegrationAction');
		$this->_form->setWindow('winEditDataIntegrationActionId');
		$this->view->form = $this->_form;
		$this->view->action = $this->_action;
		$this->view->callback = $this->_getParam('callback','');
		$this->render('edit-action');
	}

	/**
	 * Process modifications on action edit
	 */
	public function processEditActionAction() {
		$this->editActionAction();
		$params = $this->_getParam('dataIntegrationAction');
		$this->_action->populateWithArray($params);
		$this->_action->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-action');
	}

	/**
	 * Render edit destination
	 */
	public function editDestinationAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$this->_destination = new DataIntegrationDestination();
		if ($id > 0) {
			$this->_destination->dataIntegrationDestinationId = $id;
			$this->_destination->populate();
		}
		$this->_destination->handlerType = $type;
		$this->_form = new WebVista_Form(array('name'=>'edit-destination'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'data-integration.raw/process-edit-destination');
		$this->_form->loadORM($this->_destination,'destination');
		$this->_form->setWindow('winEditDataIntegrationDestinationId');
		$this->view->form = $this->_form;
		$this->view->destination = $this->_destination;
		$this->view->callback = $this->_getParam('callback','');
		$this->render('edit-destination');
	}

	/**
	 * Process modifications on destination edit
	 */
	public function processEditDestinationAction() {
		$this->editDestinationAction();
		$params = $this->_getParam('destination');
		$this->_destination->populateWithArray($params);
		$this->_destination->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-destination');
	}

}
