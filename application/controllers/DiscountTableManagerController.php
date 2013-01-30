<?php
/*****************************************************************************
*       DiscountTableManagerController.php
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


class DiscountTableManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function toolbarAction() {
		// utilize the common toolbar method defined at WebVista_Controller_Action
		$this->_renderToolbar('toolbar');
	}

	protected function _generateRowData(DiscountTable $discountTable) {
		$row = array();
		$row['id'] = $discountTable->guid;
		$row['data'] = array();
		$row['data'][] = $discountTable->name;
		$row['data'][] = $discountTable->dateStart;
		$row['data'][] = $discountTable->dateEnd;
		return $row;
	}

	public function listAction() {
		$discountTable = new DiscountTable();
		$discountTableIterator = $discountTable->getIteratorByDistinctGuid();
		$rows = array();
		foreach ($discountTableIterator as $discount) {
			$rows[] = $this->_generateRowData($discount);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function addAction() {
		$this->_renderEdit();
	}

	public function editAction() {
		$id = $this->_getParam('id');
		$this->_renderEdit($id);
	}

	protected function _renderEdit($id='',$action='add') {
		$discountTable = new DiscountTable();
		if (strlen($id) > 0) {
			$discountTable->guid = $id;
			$discountTable->populateByGuid();
		}
		else {
			$discountTable->dateStart = date('Y-m-d');
			$discountTable->dateEnd = date('Y-m-d',strtotime('+30 days'));
		}
		$form = new WebVista_Form(array('name'=>'editDiscountTable'));
                $form->setAction(Zend_Registry::get('baseUrl').'discount-table-manager.raw/process-'.$action);
		$form->loadORM($discountTable,'discountTable');
		$form->setWindow('windowEditDiscountTableId');
		$this->view->form = $form;
		$this->view->discountTypes = $discountTable->discountTypes;
		$this->render('edit');
	}

	public function processAddAction() {
		$this->_processEdit();
	}

	public function processEditAction() {
	}

	protected function _processEdit() {
		$params = $this->_getParam('discountTable');
		$incomes = $this->_getParam('incomes');
		$oldGuid = $this->_getParam('oldGuid');
		$data = __('No inputs found');
		if (is_array($params)) {
			$discountTable = new DiscountTable();
			if (isset($params['guid'])) {
				if ($oldGuid != $params['guid'] && $oldGuid != '') {
					if (!strlen($params['guid']) > 0) {
						$params['guid'] = str_replace('-','',NSDR::create_guid());
					}
					$discountTable->updateGuid($oldGuid,$params['guid']);
				}
				$discountTable->guid = $params['guid'];
				$discountTable->populateByGuid();
			}
			$discountTable->populateWithArray($params);
			if ($discountTable->hasConflicts()) {
				$data = __('Please choose different insurance programs or date of service.');
			}
			else {
				$ctr = 1;
				foreach ($incomes as $key=>$value) {
					$discountTable->populateWithArray($value);
					$discountTable->discountId = $ctr++;
					$discountTable->persist();
				}
				$data = $this->_generateRowData($discountTable);
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$id = preg_replace('/[^0-9a-z_A-Z- \.]/','',$this->_getParam('id'));
		$discountTable = new DiscountTable();
		$discountTable->guid = $id;
		$discountTable->deleteByGuid();
		$data = $this->_generateRowData($discountTable);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateDiscountRowData(DiscountTable $discountTable) {
		$row = array();
		$row['id'] = $discountTable->discountId;
		$row['data'] = array();
		$row['data'][] = $discountTable->discount.'';
		$row['data'][] = $discountTable->discountType.'';
		$row['data'][] = $discountTable->income1;
		$row['data'][] = $discountTable->income2;
		$row['data'][] = $discountTable->income3;
		$row['data'][] = $discountTable->income4;
		$row['data'][] = $discountTable->income5;
		$row['data'][] = $discountTable->income6;
		$row['data'][] = $discountTable->income7;
		$row['data'][] = $discountTable->income8;
		$row['data'][] = $discountTable->income9;
		$row['data'][] = $discountTable->income10;
		return $row;
	}

	public function listDiscountAction() {
		$id = $this->_getParam('id');
		$discountTable = new DiscountTable();
		$rows = array();
		$discountTable->discountId = 'incomeSize';
		$rows[] = $this->_generateDiscountRowData($discountTable);
		$discountTableIterator = $discountTable->getIteratorByGuid($id);
		$firstRow = false;
		foreach ($discountTableIterator as $discount) {
			if (!$firstRow) {
				$firstRow = true;
				$rows[0]['data'][2] = $discount->familySize1;
				$rows[0]['data'][3] = $discount->familySize2;
				$rows[0]['data'][4] = $discount->familySize3;
				$rows[0]['data'][5] = $discount->familySize4;
				$rows[0]['data'][6] = $discount->familySize5;
				$rows[0]['data'][7] = $discount->familySize6;
				$rows[0]['data'][8] = $discount->familySize7;
				$rows[0]['data'][9] = $discount->familySize8;
				$rows[0]['data'][10] = $discount->familySize9;
				$rows[0]['data'][11] = $discount->familySize10;
			}
			$rows[] = $this->_generateDiscountRowData($discount);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listInsuranceProgramsAction() {
		$guid = $this->_getParam('id');
		$discountTable = new DiscountTable();
		$discountTable->guid = $guid;
		$discountTable->populateByGuid();
		$ids = array();
		foreach (explode(',',$discountTable->insuranceProgramIds) as $id) {
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

}
