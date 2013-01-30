<?php
/*****************************************************************************
*       StatisticsManagerController.php
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


class StatisticsManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$psd = new PatientStatisticsDefinition();
		$this->view->types = $psd->_types;
		$this->render();
	}

	public function editAction() {
		$id = (int)$this->_getParam('id');
		$psd = new PatientStatisticsDefinition();
		if ($id > 0) {
			$psd->patientStatisticsDefinitionId = $id;
			$psd->populate();
		}
		$form = new WebVista_Form(array('name'=>'stats'));
		$form->setAction(Zend_Registry::get('baseUrl').'statistics-manager.raw/process-edit');
		$form->loadORM($psd,'stats');
		$form->setWindow('winEditStatsId');
		$this->view->form = $form;
		$this->view->ORM = $psd;
		$this->view->types = $psd->_types;
		$this->render();
	}

	public function processEditAction() {
		$params = $this->_getParam('stats');
		$psd = new PatientStatisticsDefinition();
		if (!isset($params['guid']) || !(strlen($params['guid']) > 0)) {
			$params['guid'] = NSDR::create_guid();
		}
		$data = array();
		$id = (int)$params['patientStatisticsDefinitionId'];
		if ($id > 0) {
			$psd->patientStatisticsDefinitionId = $id;
			$psd->populate();
			if (isset($params['name']) && $psd->isNameExists($params['name'])) {
				$data['error'] = __('Name already exists').': '.$params['name'];
			}
		}
		$psd->populateWithArray($params);
		if (!ctype_alpha(substr($psd->name,0,1))) {
			$data['error'] = __('Invalid name').': '.$psd->name;
		}

		if (!isset($data['error'])) {
			$psd->persist();
			$data = $this->_generateStatsRow($psd);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateStatsRow(PatientStatisticsDefinition $psd) {
		$row = array();
		$row['id'] = $psd->patientStatisticsDefinitionId;
		$row['data'] = array();
		$row['data'][] = $psd->displayName;
		$row['data'][] = $psd->type;
		$row['data'][] = $psd->displayedValue;
		$row['data'][] = $psd->active;
		return $row;
	}

	public function listAction() {
		$data = array();
		$psd = new PatientStatisticsDefinition();
		$psdIterator = $psd->getIterator();
		foreach ($psdIterator as $row) {
			$data[] = $this->_generateStatsRow($row);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$data));
	}

	public function processDeleteAction() {
		$data = false;
		$id = (int)$this->_getParam('id');
		$psd = new PatientStatisticsDefinition();
		if ($id > 0) {
			$psd->patientStatisticsDefinitionId = $id;
			$psd->setPersistMode(WebVista_Model_ORM::DELETE);
			$psd->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}

