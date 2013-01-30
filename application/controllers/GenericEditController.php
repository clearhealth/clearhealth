<?php
/*****************************************************************************
*       GenericEditController.php
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
 * Generic Edit controller
 */
class GenericEditController extends WebVista_Controller_Action {

	protected $_ormObject = null;

	/**
	 * Default action to dispatch
	 */
	public function indexAction() {
		$enumerationId = (int)$this->_getParam("enumerationId");
		$enumeration = new Enumeration();
		$enumeration->enumerationId = $enumerationId;
		$enumeration->populate();
		$ormClass = $enumeration->ormClass;
		$ormId = $enumeration->ormId;
		$ormEditMethod = $enumeration->ormEditMethod;

		if (!class_exists($ormClass)) {
			$ormClass = 'Enumeration';
			$ormEditMethod = 'ormEditMethod';
		}
		$ormObject = new $ormClass();
		if (!$ormObject instanceof ORM) {
			throw new Exception("ORM Class {$ormClass} is not an instance of an ORM");
		}
		if (strlen($ormEditMethod) > 0 && method_exists($ormObject,$ormEditMethod)) {
			$isAdd = (int)$this->_getParam('isAdd'); // isAdd is a flag to add new enum and ORM to parent's $ormId
			$form = $ormObject->$ormEditMethod($ormId,$isAdd);
		}
		else {
			foreach ($ormObject->_primaryKeys as $key) {
				$ormObject->$key = $ormId;
			}
			$ormObject->populate();
			$form = new WebVista_Form(array('name' => 'edit-object'));
			$form->setAction(Zend_Registry::get('baseUrl') . "generic-edit.raw/process-edit?enumerationId={$enumerationId}");
			$form->loadORM($ormObject, "ormObject");
			$form->setWindow('windowEditORMObjectId');
		}
		$this->_ormObject = $ormObject;
		$this->view->ormObject = $this->_ormObject;
		$this->view->form = $form;
		$this->render('index');
	}

	public function processEditAction() {
		try {
			$this->indexAction();
		}
		catch (Exception $e) {
			throw $e;
		}
		$ormObject = $this->_getParam("ormObject");
		$this->_ormObject->populateWithArray($ormObject);
		$this->_ormObject->persist();
		$this->view->message = __('Object saved successfully');
		$this->render();
	}

	// a placeholder for generic edit popup, all manipulations are in javascripts
	public function codeEditorAction() {
		$jsVar = $this->_getParam('jsVar');
                $jsVar = preg_replace('/[^a-z_0-9- ]/i','',$jsVar);
                $jsVar = ltrim(preg_replace('/^(?P<digit>\d+)/','',$jsVar));
		$this->view->jsVar = $jsVar;
		$this->render();
	}

	public function processEditByFieldAction() {
		$personId = (int)$this->_getParam("personId");
		$field = $this->_getParam('field');
		$value = $this->_getParam('value');
		$id = (int)$this->_getParam('id');
		$orm = $this->_getParam('orm');
		$obj = null;
		switch ($orm) {
			case 'patientNote':
				$obj = new PatientNote();
				$obj->patient_id = $personId;
				if ($id > 0) {
					$obj->patientNoteId = $id;
					$obj->populate();
				}
				else {
					// defaults for new note
					$obj->note_date = date('Y-m-d H:i:s');
					$obj->user_id = (int)Zend_Auth::getInstance()->getIdentity()->personId;
					$obj->priority = 5;
					$obj->active = 1;
					if ($field != 'note') {
						$obj->note = 'blank';
					}
				}
				break;
		}
		$retVal = false;
		if ($obj !== null && (in_array($field,$obj->ormFields()))) {
			$obj->$field = $value;
			$obj->persist();
			$retVal = true;
		}
		if ($retVal) {
			$data = true;
		}
		else {
			$data = array('error' => __('There was an error attempting to update the selected record.'));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$id = (int)$this->_getParam('id');
		$orm = $this->_getParam('orm');
		$obj = null;
		switch ($orm) {
			case 'patientNote':
				$obj = new PatientNote();
				$obj->patientNoteId = $id;
			break;
		}
		$ret = false;
		if ($obj !== null) {
			$obj->setPersistMode(WebVista_Model_ORM::DELETE);
			$obj->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

}

