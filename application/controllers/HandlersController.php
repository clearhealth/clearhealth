<?php
/*****************************************************************************
*       HandlersController.php
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


class HandlersController extends WebVista_Controller_Action {

	protected $_handler = null;
	protected $_customKey = '';
	protected $_customName = '';

	public function init() {
		parent::init();
		$this->_customKey = 'custom';
		$this->_customName = __('Custom');
	}

	public function listAction() {
		$type = (int)$this->_getParam('type');
		$handler = new Handler($type);
		$handlerIterator = $handler->getIterator();
		$listConditions = Handler::listConditions();
		$rows = array();
		foreach ($handlerIterator as $item) {
			$condition = $this->_customName;
			if (isset($listConditions[$item->condition]) && !strlen($item->conditionObject) > 0) {
				$condition = $listConditions[$item->condition];
			}
			$tmp = array();
			$tmp['id'] = $item->handlerId;
			switch ($type) {
				case Handler::HANDLER_TYPE_GA: // General Alert
				case Handler::HANDLER_TYPE_HSA: // Health Status Alert
					$tmp['data'][] = $item->name;
					$tmp['data'][] = $condition;
					$tmp['data'][] = $item->active;
					break;
				default: // HL7
					$tmp['data'][] = $item->name;
					$tmp['data'][] = $item->direction;
					$tmp['data'][] = $condition;
					$tmp['data'][] = $item->active;
					break;
			}
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processEditSingleAction() {
		$type = (int)$this->_getParam('type');
		$field = $this->_getParam('field');
		$id = (int)$this->_getParam('id');
		$value = preg_replace('/[^a-z_0-9- ]/i','',$this->_getParam('value',''));
		$HSAHandler = new Handler();
		$data = array();
		$data['msg'] = __('Field name '.$field.' does not exist');
		if (in_array($field,$HSAHandler->ormFields())) {
			if ($id > 0) {
				$HSAHandler->handlerId = $id;
				$HSAHandler->populate();
			}
			$HSAHandler->$field = $value;
			$HSAHandler->handlerType = $type;
			$HSAHandler->persist();
			$data['msg'] = __('Updated successfully');
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Render edit handler page
	 */
	public function editAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$this->_handler = new Handler();
		if ($id > 0) {
			$this->_handler->handlerId= $id;
			$this->_handler->populate();
		}

		$this->view->listConditions = array('');
		if (strlen($this->_handler->conditionObject) > 0) {
			$this->_handler->condition = $this->_customKey;
			$this->view->listConditions[$this->_customKey] = $this->_customName;
		}
		foreach (Handler::listConditions() as $id=>$name) {
			$this->view->listConditions[$id] = $name;
		}

		$this->_form = new WebVista_Form(array('name'=>'edit'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'handlers.raw/process-edit');
		$this->_form->loadORM($this->_handler,'handler');
		$this->_form->setWindow('winEditHandlerId');
		$this->view->form = $this->_form;
		$this->view->callback = $this->_getParam('callback','');

		$dataSource =  new DataIntegrationDatasource($type);
		if ($type === Handler::HANDLER_TYPE_HL7) {
			$dataSourceIterator = $dataSource->getIterator();
		}
		else {
			$dataSourceIterator = $dataSource->getCustomIterator();
		}
		$this->view->listDatasources = array('');
		$dataIntegrationDatasourceName = $this->_handler->dataIntegrationDatasource->name;
		$dataIntegrationDatasourceId = $this->_handler->dataIntegrationDatasource->dataIntegrationDatasourceId;
		if (strlen($dataIntegrationDatasourceName) > 0) {
			$this->view->listDatasources[$dataIntegrationDatasourceId] = $dataIntegrationDatasourceName;
		}
		foreach ($dataSourceIterator as $item) {
			$this->view->listDatasources[$item->dataIntegrationDatasourceId] = $item->name;
		}

		$template =  new DataIntegrationTemplate($type);
		if ($type === Handler::HANDLER_TYPE_HL7) {
			$templateIterator = $template->getIterator();
		}
		else {
			$templateIterator = $template->getCustomIterator();
		}
		$this->view->listTemplates = array('');
		$dataIntegrationTemplateName = $this->_handler->dataIntegrationTemplate->name;
		$dataIntegrationTemplateId = $this->_handler->dataIntegrationTemplate->dataIntegrationTemplateId;
		if (strlen($dataIntegrationTemplateName) > 0) {
			$this->view->listTemplates[$dataIntegrationTemplateId] = $dataIntegrationTemplateName;
		}
		foreach ($templateIterator as $item) {
			$this->view->listTemplates[$item->dataIntegrationTemplateId] = $item->name;
		}

		// lines below are for HL7 only
		if ($type === Handler::HANDLER_TYPE_HL7) {
			$this->view->listDirections = array('');
			foreach (Handler::listDirections() as $id=>$name) {
				$this->view->listDirections[$id] = $name;
			}

			$destination =  new DataIntegrationDestination($type);
			$destinationIterator = $destination->getIterator();
			$this->view->listDestinations = array('');
			foreach ($destinationIterator as $item) {
				$this->view->listDestinations[$item->dataIntegrationDestinationId] = $item->name;
			}

			$action =  new DataIntegrationAction($type);
			$actionIterator = $action->getIterator();
			$this->view->listActions = array('');
			foreach ($actionIterator as $item) {
				$this->view->listActions[$item->dataIntegrationActionId] = $item->name;
			}
		}
		$this->render('edit');
	}

	/**
	 * Process modifications on handler
	 */
	public function processEditAction() {
		$type = (int)$this->_getParam('type');
		$this->editAction();
		$params = $this->_getParam('handler');
		$name = $params['name'];
                // remove prefix characters if it's digit/numeric
                $name = ltrim(preg_replace('/^(?P<digit>\d+)/','',$name));
		$params['name'] = $name;
		$this->_handler->populateWithArray($params);
		$this->_handler->handlerType = $type;
		$this->_handler->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit');
	}

	/**
	 * Render edit condition object page
	 */
	public function editConditionObjectAction() {
		$id = (int)$this->_getParam('id');
		$type = (int)$this->_getParam('type');
		$this->_handler = new Handler();
		if ($id > 0) {
			$this->_handler->handlerId= $id;
			$this->_handler->populate();
		}
		// TODO: generate Object code for condition and action here...
		$this->view->codeEdited = (strlen($this->_handler->conditionObject) > 0);
		if (!$this->view->codeEdited) {
			$this->_handler->conditionObject = $this->_handler->generateDefaultConditionObject($type);
		}
		$this->_form = new WebVista_Form(array('name'=>'edit-condition-object'));
                $this->_form->setAction(Zend_Registry::get('baseUrl') . 'handlers.raw/process-edit-condition-object');
		$this->_form->loadORM($this->_handler,'handler');
		$this->_form->setWindow("winConditionObjectEditId");
		$this->view->form = $this->_form;
		$this->render('edit-condition-object');
	}

	/**
	 * Process modifications on condition object edit
	 */
	public function processEditConditionObjectAction() {
		$type = (int)$this->_getParam('type');
		$this->editConditionObjectAction();
		$params = $this->_getParam('handler');
		$array = array();
		$array['conditionObject'] = $params['conditionObject'];
		$this->_handler->populateWithArray($array);
		$this->_handler->handlerType = $type;
		$ret = $this->_isValidConditionObject($type);
		if ($ret === true) {
			$this->view->message = 200 . '|' . __('Record saved successfully');
			$this->_handler->persist();
		}
		else {
			$this->view->message = 400 . '|' . $ret;
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($this->view->message);
	}

	protected function _isValidConditionObject() {
		$type = $this->_handler->handlerType;
		$handlerName = Handler::normalizeHandlerName($this->_handler->name);
		$conditionObject = $this->_handler->conditionObject;
		if (!strlen($conditionObject) > 0) {
			return __('Condition object is required');
		}
		eval($conditionObject);
		$ret = true;
		do {
			$classConditionHandler = $handlerName.'ConditionHandler';
			if (!ProcessAlert::isParentOf($classConditionHandler,'DataIntegrationConditionHandlerAbstract')) {
				$ret = __($classConditionHandler.' does not exists or not an instance of DataIntegrationConditionHandlerAbstract');
				break;
			}
			/*$classDatasource = $handlerName.'DataIntegrationDatasource';
			if (!ProcessAlert::isParentOf($classDatasource,'DataIntegrationDatasourceAbstract')) {
				$ret = __($classDatasource.' does not exists or not an instance of DataIntegrationDatasourceAbstract');
				break;
			}
			// code below is for HL7 only
			if ($type !== Handler::HANDLER_TYPE_HL7) {
				break;
			}
			$classAction = $handlerName.'DataIntegrationAction';
			if (!ProcessAbstract::isParentOf($classAction,'DataIntegrationActionAbstract')) {
				$ret = __($classAction.' does not exists or not an instance of DataIntegrationActionAbstract');
				break;
			}
			$classDestination = $handlerName.'DataIntegrationDestination';
			if (!ProcessAbstract::isParentOf($classDestination,'DataIntegrationDestinationAbstract')) {
				$ret = __($classDestination.' does not exists or not an instance of DataIntegrationDestinationAbstract');
				break;
			}*/
		} while(false);
		return $ret;
	}

}
