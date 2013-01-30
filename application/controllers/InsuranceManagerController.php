<?php
/*****************************************************************************
*       InsuranceManagerController.php
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
 * Insurance Manager controller
 */
class InsuranceManagerController extends WebVista_Controller_Action {

	protected $_form = null;
	protected $_company = null;
	protected $_program = null;

	/**
	 * Default action to dispatch
	 */
	public function indexAction() {
		$this->render('index');
	}

	public function listAction() {
		$rows = array();

		$company = new Company();
		$companyIterator = $company->getIterator();
		foreach ($companyIterator as $companyRow) {
			$row = array();
			$row['id'] = $companyRow->companyId;
			$row['data'][] = $companyRow->name;
			$insuranceProgram = new InsuranceProgram();
			$insuranceProgramIterator = $insuranceProgram->getIteratorByCompanyId($companyRow->companyId);
			foreach ($insuranceProgramIterator as $program) {
				$tmp = array();
				$tmp['id'] = $program->insuranceProgramId;
				$tmp['data'][] = $program->name;
				$row['rows'][] = $tmp;
			}
			$rows[] = $row;
		}

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$objId = (int)$this->_getParam('objId');
		$id = (int)$this->_getParam('id');
		$type = $this->_getParam('type');
		$orm = null;
		switch ($type) {
			case 'company':
				$orm = new Company();
				$orm->companyId = $id;
				break;
			case 'program':
				$orm = new InsuranceProgram();
				$orm->insuranceProgramId = $id;
				break;
			case 'address':
				$orm = new Address();
				$orm->addressId = $id;
				break;
			case 'phone':
				$orm = new PhoneNumber();
				$orm->numberId = $id;
				break;
			case 'note':
				$orm = new PatientNote();
				$orm->patientNoteId = $id;
				break;
			case 'programIdentifier':
				$orm = new BuildingProgramIdentifier();
				$orm->programId = $objId;
				$orm->buildingId = $id;
				break;
		}
		$ret = false;
		if ($orm !== null) {
			$orm->setPersistMode(WebVista_Model_ORM::DELETE);
			$orm->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processAddCompanyAction() {
		$params = $this->_getParam('company');
		$company = new Company();
		$company->populateWithArray($params);
		$company->persist();
		$ret = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processAddProgramAction() {
		$params = $this->_getParam('program');
		$program = new InsuranceProgram();
		$program->populateWithArray($params);
		$program->persist();
		$ret = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function editCompanyAction() {
		$id = (int)$this->_getParam('id');
		$this->_company = new Company();
		if ($id > 0) {
			$this->_company->companyId = $id;
			$this->_company->populate();
		}
		$this->_form = new WebVista_Form(array('name'=>'edit-company'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'insurance-manager.raw/process-edit-company');
		$this->_form->loadORM($this->_company,'company');
		$this->_form->setWindow('winEditCompanyId');
		$this->view->form = $this->_form;
		$this->view->email = $this->_company->_companyEmail;
		$this->view->statesList = Address::getStatesList();
		$this->view->phoneTypes = PhoneNumber::getListPhoneTypes();
		$this->view->addressTypes = Address::getListAddressTypes();
		$this->render('edit-company');
	}

	public function processEditCompanyAction() {
		$this->editCompanyAction();
		$params = $this->_getParam('company');
		$this->_company->populateWithArray($params);
		$this->_company->_companyEmail = $this->_getParam('email');
		$this->_company->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-company');
	}

	public function editProgramAction() {
		$id = (int)$this->_getParam('id');
		$companyId = (int)$this->_getParam('companyId');
		$this->_program = new InsuranceProgram();
		if ($id > 0) {
			$this->_program->insuranceProgramId = $id;
			$this->_program->populate();
		}
		else {
			$this->_program->companyId = $companyId;
		}
		$this->_form = new WebVista_Form(array('name'=>'edit-program'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'insurance-manager.raw/process-edit-program');
		$this->_form->loadORM($this->_program,'program');
		$this->_form->setWindow('winEditProgramId');
		$this->view->form = $this->_form;
		$this->view->buildings = $this->_getBuildings();
		$insurancePrefList = InsuranceProgram::getListInsurancePreferences();
		$payerTypes = array(''=>'');
		if (isset($insurancePrefList[InsuranceProgram::INSURANCE_PAYER_TYPE_ENUM_KEY])) {
			foreach ($insurancePrefList[InsuranceProgram::INSURANCE_PAYER_TYPE_ENUM_KEY] as $key=>$value) $payerTypes[$key] = $value;
		}
		$this->view->payerTypes = $payerTypes;
		$programTypes = array(''=>'');
		if (isset($insurancePrefList[InsuranceProgram::INSURANCE_PROGRAM_TYPE_ENUM_KEY])) {
			foreach ($insurancePrefList[InsuranceProgram::INSURANCE_PROGRAM_TYPE_ENUM_KEY] as $key=>$value) $programTypes[$key] = $value;
		}
		$this->view->programTypes = $programTypes;
		$fundsSources = array(''=>'');
		if (isset($insurancePrefList[InsuranceProgram::INSURANCE_FUNDS_SOURCE_ENUM_KEY])) {
			foreach ($insurancePrefList[InsuranceProgram::INSURANCE_FUNDS_SOURCE_ENUM_KEY] as $key=>$value) $fundsSources[$key] = $value;
		}
		$this->view->fundsSources = $fundsSources;
		$this->view->feeScheduleDisplay = $this->_program->displayFeeSchedule;

		$company = new Company();
		$addressIterator = $company->getAddressIterator($companyId);
		$addresses = array(''=>'');
		foreach ($addressIterator as $addr) {
			$addresses[$addr->addressId] = $addr->name;
		}
		$this->view->addresses = $addresses;
		$this->render('edit-program');
	}

	public function processEditProgramAction() {
		$this->editProgramAction();
		$params = $this->_getParam('program');
		$this->_program->populateWithArray($params);
		$this->_program->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-program');
	}

	public function getMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-menu');
	}

	public function processEditByFieldAction() {
		$objId = (int)$this->_getParam("objId");
		$type = $this->_getParam("type");
		$id = (int)$this->_getParam("id");
		$field = $this->_getParam("field");
		$value = $this->_getParam("value");

		$orm = null;
		switch ($type) {
			case 'address':
				$orm = new CompanyAddress();
				$orm->companyId = $objId;
				$ormLink = 'address';
				break;
			case 'phone':
				$orm = new CompanyNumber();
				$orm->companyId = $objId;
				$ormLink = 'number';
				break;
			case 'programIdentifier':
				$orm = new BuildingProgramIdentifier();
				$orm->programId = $objId;
				$orm->buildingId = $id;
				if (!$id > 0) {
					$buildings = $this->_getBuildings();
					list($buildingId,$buildName) = each($buildings);
					$programIdentifier = new BuildingProgramIdentifier();
					$programIdentifierIterator = $programIdentifier->getIteratorByProgramId($objId);
					foreach ($programIdentifierIterator as $item) {
						unset($buildings[$item->buildingId]);
					}
					if (count($buildings) > 0) {
						list($buildingId,$buildName) = each($buildings);
						$orm->buildingId = $buildingId;
					}
				}
				$ormLink = 'building';
				break;
			default:
				break;
		}

		$retVal = false;
		if ($orm !== null && (in_array($field,$orm->ormFields()) || in_array($field,$orm->$ormLink->ormFields()))) {
			if ($id > 0) {
				$primaryKey = $ormLink.'Id';
				$orm->$ormLink->$primaryKey = $id;
				$orm->populate();
			}
			if ($field == 'number') {
				$orm->number->$field = $value;
			}
			else {
				$orm->$field = $value;
			}
			$orm->persist();
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

	public function listPhonesAction() {
		$companyId = (int)$this->_getParam('companyId');
		$rows = array();
		$company = new Company();
		$phoneNumberIterator = $company->getPhoneNumberIterator($companyId);
		foreach ($phoneNumberIterator as $phone) {
			$rows[] = $this->_toJSON($phone,'phoneNumberId',array('name','type','number','notes','active'));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listAddressesAction() {
		$companyId = (int)$this->_getParam('companyId');
		$rows = array();
		$company = new Company();
		$addressIterator = $company->getAddressIterator($companyId);
		foreach ($addressIterator as $addr) {
			$rows[] = $this->_toJSON($addr,'addressId',array('name','type','line1','line2','city','state','postal_code','notes','active'));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listProgramIdentifiersAction() {
		$programId = (int)$this->_getParam('programId');
		$rows = array();
		$programIdentifier = new BuildingProgramIdentifier();
		$programIdentifierIterator = $programIdentifier->getIteratorByProgramId($programId);
		$buildings = $this->_getBuildings();
		foreach ($programIdentifierIterator as $item) {
			$tmp = array();
			$tmp['id'] = $item->buildingId;
			$tmp['data'][] = isset($buildings[$item->buildingId])?$buildings[$item->buildingId]:'';
			$tmp['data'][] = $item->identifier;
			$tmp['data'][] = $item->x12SenderId;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	protected function _getBuildings() {
		$building = new Building();
		$buildingIterator = $building->getIterator();
		$buildings = array();
		foreach ($buildingIterator as $item) {
			$buildings[$item->id] = $item->name;
		}
		return $buildings;
	}

	protected function _toJson(ORM $obj,$key,Array $fields) {
		$data = array(
			'id'=>$obj->$key,
			'data'=>array(),
		);
		foreach ($fields as $field) {
			$data['data'][] = (string)$obj->$field;
		}
		return $data;
	}

	protected function _processEdit(ORM $obj,$subOrm,$key,Array $fields,Array $values) {
		if (isset($values[$key])) {
			$obj->$subOrm->$key = (int)$values[$key];
			$obj->$subOrm->populate();
		}
		$obj->populateWithArray($values);
		if (isset($values[$subOrm])) $obj->$subOrm->populateWithArray($values[$subOrm]); // this must be required and must occur after parent's populate
		$obj->persist();
		$data = $this->_toJSON($obj->$subOrm,$key,$fields);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processAddPhoneAction() {
		$params = $this->_getParam('phone');
		$this->_processEditPhone($params);
	}

	public function processEditPhoneAction() {
		$params = $this->_getParam('phone');
		$this->_processEditPhone($params);
	}

	protected function _processEditPhone(Array $params) {
		$obj = new CompanyNumber();
		$this->_processEdit($obj,'number','phoneNumberId',array('name','type','number','notes','active'),$params);
	}

	public function processAddAddressAction() {
		$params = $this->_getParam('address');
		$this->_processEditAddress($params);
	}

	public function processEditAddressAction() {
		$params = $this->_getParam('address');
		$this->_processEditAddress($params);
	}

	protected function _processEditAddress(Array $params) {
		$obj = new CompanyAddress();
		$this->_processEdit($obj,'address','addressId',array('name','type','line1','line2','city','state','postal_code','notes','active'),$params);
	}

}
