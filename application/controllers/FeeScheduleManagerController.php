<?php
/*****************************************************************************
*       FeeScheduleManagerController.php
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


class FeeScheduleManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	protected function _generateRowData(FeeSchedule $fs) {
		$ret = array();
		$ret['id'] = $fs->guid;
		$ret['data'] = array();
		$ret['data'][] = $fs->name;
		$ret['data'][] = date('Y-m-d',strtotime($fs->dateOfServiceStart));
		$ret['data'][] = date('Y-m-d',strtotime($fs->dateOfServiceEnd));
		$insurancePrograms = InsuranceProgram::getInsuranceProgramsByIds($fs->insuranceProgramIds);
		$ret['data'][] = implode(', ',$insurancePrograms);
		$ips = array();
		foreach (explode(',',$fs->insuranceProgramIds) as $id) {
			$id = (int)$id;
			if (!$id > 0) continue;
			$ips[$id] = isset($insurancePrograms[$id])?$insurancePrograms[$id]:'';
		}
		$ret['userdata']['IPs'] = $ips;
		return $ret;
	}

	public function listAction() {
		$rows = array();
		$feeSchedule = new FeeSchedule();
		foreach ($feeSchedule->getIteratorByDistinctGuid() as $fs) {
			$rows[] = $this->_generateRowData($fs);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processDeleteAction() {
		$param = $this->_getParam('id');
		$ids = explode(',',$param);
		foreach ($ids as $guid) {
			if (!strlen($guid) > 0) continue;
			$fs = new FeeSchedule();
			$fs->guid = $guid;
			$fs->setPersistMode(WebVista_Model_ORM::DELETE);
			$fs->persist(false);
		}
		$data = array();
		$data['msg'] = __('Record deleted successfully');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editAction() {
		$guid = $this->_getParam('id');
		$fs = new FeeSchedule();
		if (strlen($guid) > 0) {
			$fs->guid = $guid;
			$fs->populateByGuid();
		}
		if ($fs->dateOfServiceStart == '' || $fs->dateOfServiceStart == '0000-00-00 00:00:00') {
			$fs->dateOfServiceStart = date('Y-m-d');
			$fs->dateOfServiceEnd = date('Y-m-d',strtotime('+7 days'));
		}
		$form = new WebVista_Form(array('name'=>'edit-fee-schedule'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'fee-schedule-manager.raw/process-edit');
		$form->loadORM($fs,'feeSchedule');
		$form->setWindow('windowEditFeeScheduleId');
		$this->view->form = $form;
		$this->render();
	}

	public function processEditAction() {
		$params = $this->_getParam('feeSchedule');
		$oldGuid = $this->_getParam('oldGuid');
		$fs = new FeeSchedule();
		if (isset($params['guid'])) {
			if ($oldGuid != $params['guid'] && $oldGuid != '') {
				if (!strlen($params['guid']) > 0) {
					$params['guid'] = str_replace('-','',NSDR::create_guid());
				}
				$fs->updateGuid($oldGuid,$params['guid']);
			}
			$fs->guid = $params['guid'];
			$fs->populateByGuid();
		}
		$fs->populateWithArray($params);
		try {
			$fs->persist(false);
			$ret = $this->_generateRowData($fs);
		}
		catch (Exception $e) {
			$fs->populateByGuid();
			$ret = $e->getMessage();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function editFeeAction() {
		$guid = $this->_getParam('guid');
		$code = $this->_getParam('code');
		$fs = new FeeSchedule();
		$fs->guid = $guid;
		$fs->procedureCode = $code;
		$fs->populateByGuidCode();
		if ($fs->dateObsolete == '' || $fs->dateObsolete == '0000-00-00 00:00:00') {
			$fs->dateObsolete = $fs->dateOfServiceEnd;
		}
		$form = new WebVista_Form(array('name'=>'editFee'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'fee-schedule-manager.raw/process-edit-fee');
		$form->loadORM($fs,'fee');
		$form->setWindow('windowEditFeeId');
		$this->view->form = $form;
		$this->render();
	}

	public function processEditFeeAction() {
		$params = $this->_getParam('fee');
		$fs = new FeeSchedule();
		$guid = isset($params['guid'])?$params['guid']:'';
		$code = isset($params['procedureCode'])?$params['procedureCode']:'';
		$fs = new FeeSchedule();
		$fs->guid = $guid;
		$fs->procedureCode = $code;
		$fs->populateByGuidCode();
		$fs->populateWithArray($params);
		try {
			$fs->persist();
			$ret = $this->_generateFeeRowData($fs);
		}
		catch (Exception $e) {
			$ret = $e->getMessage();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function insuranceSelectAutoCompleteAction() {
		$match = $this->_getParam('name');
		$matches = array();
		if (!strlen($match) > 0) $this->_helper->autoCompleteDojo($matches);
		$this->_helper->autoCompleteDojo($matches);
	}

	public function setFeesAction() {
		$guid = $this->_getParam('id');
		$fs = new FeeSchedule();
		if (strlen($guid) > 0) {
			$fs->guid = $guid;
			$fs->populateByGuid();
			$fs->insuranceProgramIds = implode(', ',InsuranceProgram::getInsuranceProgramsByIds($fs->insuranceProgramIds));
		}
		$form = new WebVista_Form(array('name'=>'edit-fee-schedule'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'fee-schedule-manager.raw/process-set-fees');
		$form->loadORM($fs,'feeSchedule');
		$form->setWindow('windowEditFeeScheduleId');
		$this->view->form = $form;

		$this->render();
	}

	public function processSetModifierAction() {
		$guid = $this->_getParam('guid');
		$procedureCode = $this->_getParam('procedureCode');
		$modifier = $this->_getParam('modifier');
		$fee = $this->_getParam('fee');
		$state = (int)$this->_getParam('state');
		$fs = new FeeSchedule();
		$fs->guid = $guid;
		$fs->procedureCode = $procedureCode;
		$fs->populate();
		$ret = '';

		$mod = '';
		if ($fs->modifier1 == $modifier) {
			$mod = 'modifier1';
		}
		else if ($fs->modifier2 == $modifier) {
			$mod = 'modifier2';
		}
		else if ($fs->modifier3 == $modifier) {
			$mod = 'modifier3';
		}
		else if ($fs->modifier4 == $modifier) {
			$mod = 'modifier4';
		}

		if ($state) { // add
			if ($mod == '') {
				if ($fs->modifier1 == '') {
					$fs->modifier1 = $modifier;
					$fs->modifier1fee = $fee;
				}
				else if ($fs->modifier2 == '') {
					$fs->modifier2 = $modifier;
					$fs->modifier2fee = $fee;
				}
				else if ($fs->modifier3 == '') {
					$fs->modifier3 = $modifier;
					$fs->modifier3fee = $fee;
				}
				else if ($fs->modifier4 == '') {
					$fs->modifier4 = $modifier;
					$fs->modifier4fee = $fee;
				}
				else {
					$ret = __('Maximum modifiers reached');
				}
			}
			else {
				$fs->$mod = $modifier;
				$modfee = $mod.'fee';
				$fs->$modfee = $fee;
			}
		}
		else { // remove
			if ($mod != '') {
				$fs->$mod = '';
				$modfee = $mod.'fee';
				$fs->$modfee = 0;
			}
		}
		if ($ret == '') {
			$fs->persist();
			$fs->populate();
			$ret = $this->_generateFeeRowData($fs);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	protected function _generateFeeRowData(FeeSchedule $fs) {
		$ret = array();
		$ret['id'] = $fs->procedureCode;
		$ret['data'] = array();
		$ret['data'][] = $fs->procedureCode;
		$ret['data'][] = $fs->mappedCode;
		$ret['data'][] = $fs->fee;
		$ret['data'][] = implode(', ',$fs->modifiers);
		$ret['data'][] = $fs->dateObsolete;
		return $ret;
	}

	public function processSetFeesAction() {
		$params = $this->_getParam('feeSchedule');
		$fs = new FeeSchedule();
		$ret = '';
		if (isset($params['guid']) && isset($params['procedureCode'])) {
			$fs->guid = $params['guid'];
			$fs->procedureCode = $params['procedureCode'];
			$fs->populate();
			$fs->populateWithArray($params);
			$fs->persist();
			$fs->populate();
			$ret = $this->_generateFeeRowData($fs);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function toolbarAction() {
		// utilize the common toolbar method defined at WebVista_Controller_Action
		$this->_renderToolbar('toolbar');
	}

	public function listInsuranceProgramsAction() {
		$guid = $this->_getParam('fid');
		$fs = new FeeSchedule();
		$fs->guid = $guid;
		$fs->populateByGuid();
		$ids = array();
		foreach (explode(',',$fs->insuranceProgramIds) as $id) {
			$ids[$id] = $id;
		}
		$rows = array();
		foreach (InsuranceProgram::getInsurancePrograms() as $id=>$value) {
			$checked = '';
			if (isset($ids[$id])) {
				$checked = '1';
			}
			$rows[] = array(
				'id'=>$id,
				'data'=>array($checked,$value),
				'userdata'=>array('program'=>$value),
			);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listFeesAction() {
		$guid = $this->_getParam('id');
		$filter = $this->_getParam('filter',null);
		$fs = new FeeSchedule();
		if (strlen($guid) > 0) {
			$fs->guid = $guid;
			$fs->populateByGuid();
		}
		$rows = array();
		if ($filter !== null) {
			$filter = str_replace('%_','%',$filter);
			$iterator = $fs->getIteratorByFilters($filter);
		}
		else {
			$iterator = $fs->getIteratorByGuid();
		}
		foreach ($iterator as $row) {
			$rows[] = $this->_generateFeeRowData($row);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processSetDefaultFeeAction() {
		$guid = $this->_getParam('guid');
		$fee = $this->_getParam('fee');
		$fs = new FeeSchedule();
		$data = __('Failed to set default fee.');
		if ($fs->setDefaultFee($fee,$guid)) {
			$data = __('Default fee successfully set.');
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listModifiersAction() {
		$guid = $this->_getParam('guid');
		$code = $this->_getParam('code');
		$fs = new FeeSchedule();
		$fs->guid = $guid;
		$fs->procedureCode = $code;
		$fs->populateByGuidCode();
		// ab377de7-8ea7-4912-a27b-2f9749499204 = coding preferences, 9eb793f8-1d5d-4ed5-959d-1e238361e00a = visit type section
		$enumeration = new Enumeration();
		$enumeration->guid = '2b15d494-dce4-4d27-89b5-ddd6f6fc1439';
		$enumeration->populateByGuid();
		$closure = new EnumerationClosure();
		$descendants = $closure->getAllDescendants($enumeration->enumerationId,1,true);
		$rows = array();
		foreach ($descendants as $enum) {
			$mod = '';
			$fee = '';
			switch ($enum->key) {
				case $fs->modifier1:
					$fee = $fs->modifier1fee;
					break;
				case $fs->modifier2:
					$fee = $fs->modifier2fee;
					break;
				case $fs->modifier3:
					$fee = $fs->modifier3fee;
					break;
				case $fs->modifier4:
					$fee = $fs->modifier4fee;
					break;
			}
			if ($fee != '') {
				$mod = '1';
			}
			else {
				$fee = '0.00';
			}
			$row = array();
			$row['id'] = $enum->key;
			$row['data'] = array();
			$row['data'][] = $mod;
			$row['data'][] = $enum->key.': '.$enum->name;
			$row['data'][] = $fee;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

}
