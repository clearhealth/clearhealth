<?php
/*****************************************************************************
*       EducationResourcesController.php
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


class EducationResourcesController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->editAction();
	}

	public function editAction() {
		//$ormId = (int)$this->_getParam('ormId');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$edu = new EducationResource();
		$edu->educationResourceId = $enumerationId;
		$edu->populate();
		$form = new WebVista_Form(array('name'=>'education'));
		$form->setAction(Zend_Registry::get('baseUrl').'education-resources.raw/process-edit');
		$form->loadORM($edu,'education');
		$form->setWindow('windowEditORMObjectId');
		$this->view->form = $form;
		$this->render();
	}

	public function processEditAction() {
		$params = $this->_getParam('education');
		$data = false;
		if (is_array($params)) {
			$edu = new EducationResource();
			if (isset($params['educationResourceId'])) {
				$edu->educationResourceId = (int)$params['educationResourceId'];
				$edu->populate();
			}
			$edu->populateWithArray($params);
			if (!$edu->dateTime || $edu->dateTime == '0000-00-00 00:00:00') {
				$edu->dateTime = date('Y-m-d H:i:s');
			}
			$edu->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
