<?php
/*****************************************************************************
*       CalendarController.php
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
 * Calendar controller
 */
class CalendarController extends WebVista_Controller_Action {

	protected $_session;

	const FILTER_MINUTES_INTERVAL = 15;
	const FILTER_TIME_START = '07:00';
	const FILTER_TIME_END = '17:00';
	const TAB_NAME = 'Calendar';

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function listEventsAction() {
		$colIndex = $this->_getParam('colIndex');
		$filterColumns = $this->_session->filter->columns;
		$columns = array();
		if (strlen($colIndex) > 0) {
			$cols = explode(',',$colIndex);
			foreach ($cols as $col) {
				if (isset($filterColumns[$col])) {
					$columns[$col] = $filterColumns[$col];
				}
			}
		}
		else {
			$columns = $filterColumns;
		}
		$data = array();
		foreach ($columns as $index => $col) {
			$data[$index] = $this->generateEventColumnData($index,true);
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}


    /**
     * Default action to dispatch
     */
    public function indexAction() {
        $this->viewDayAction();
    }

    public function ajaxGenerateTimeColumnDataAction() {
        $data = $this->generateTimeColumnData();
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;
        $json->direct($data);
    }

    public function ajaxGenerateEventColumnDataAction() {
	calcTS();
	$columnIndex = $this->_getParam('columnIndex');
       	trigger_error("before generate column: " . calcTS(),E_USER_NOTICE);
        $data = $this->generateEventColumnData($columnIndex);
        trigger_error("after generate column: " .calcTS(),E_USER_NOTICE);
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;
        $json->direct($data);
    }

	public function ajaxStoreAppointmentAction() {
		$appointmentId = $this->_getParam('appointmentId');
		$columnId = $this->_getParam('columnId');
		$filter = $this->getCurrentDisplayFilter($columnId);

		$app = new Appointment();
		$app->appointmentId = $appointmentId;
		$app->populate();

		$arr = array();
		$arr['appointmentId'] = $app->appointmentId;
		$arr['lastChangeDate'] = $app->lastChangeDate;
		$arr['patientId'] = $app->patientId;
		$arr['title'] = $app->title;

		$this->_session->storageAppointments[$appointmentId] = $arr;

		$data = array();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxCheckAppointmentAction() {
		$appointmentId = $this->_getParam('appointmentId');
		$filter = $this->getCurrentDisplayFilter();
		$exists = false;
		foreach ($filter->columns as $index => $col) {
			if (isset($this->_session->currentAppointments[$index][$appointmentId])) {
				$exists = true;
				break;
			}
		}

		$data = array();
		$data['existsInOtherColumn'] = $exists;
		if ($exists) {
			$data['columnId'] = $index;
		}
		$app = new Appointment();
		$app->appointmentId = $appointmentId;
		$app->populate();
		$hasChanged = false;
		if (isset($this->_session->storageAppointments[$appointmentId])) {
			$row = $this->_session->storageAppointments[$appointmentId];
			if ($row['lastChangeDate'] != $app->lastChangeDate) {
				$hasChanged = true;
				$patient = new Patient();
				$patient->setPersonId($row['patientId']);
				$patient->populate();
				$person = $patient->person;
				$data['recentChanges'] = "{$app->start} - {$app->end}\n {$person->last_name}, {$person->first_name} (#{$row['patientId']}) \n {$row['title']}";
			}
		}
		$data['hasChanged'] = $hasChanged;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getColumnHeadersAction() {
		$rows = array();
		$filter = $this->getCurrentDisplayFilter();
		foreach ($filter->columns as $index=>$column) {
			$rows[$index] = $this->_generateColumnHeader($column);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($rows);
	}

	protected function _generateColumnHeader(Array $column) {
		$data = array();
		$data['header'] = "{$column['dateFilter']}<br>";
		$title = $column['dateFilter'];
		// temporarily set the header as providerId
		$providerId = $column['providerId'];
		$roomId = 0;
		if (isset($column['roomId'])) {
			$roomId = $column['roomId'];
		}
		if ($providerId > 0) {
			$provider = new Provider();
			$provider->setPersonId($providerId);
			$provider->populate();
			$name = $provider->last_name.', '.$provider->first_name;
			// we simply replace the comma with its html equivalent (&#44;) because this may cause not to render the header
			$data['header'] .= str_replace(',','&#44;',$name);
			$title .= ' -> '.$name;
		}
		if ($roomId > 0) {
			$room = new Room();
			$room->id = $roomId;
			$room->populate();
			if ($providerId > 0) {
				$data['header'] .= '<br>';
			}
			$data['header'] .= $room->name;
			$title .= ' -> '.$room->name;
		}
		$dateStart = $column['dateFilter'].' 00:00:00';
		$dateEnd = $column['dateFilter'].' 23:59:59';
		$buildingIds = ScheduleEvent::getScheduleEventByFields($providerId,$roomId,$dateStart,$dateEnd,'buildingId');
		if (count($buildingIds) > 0) {
			$buildings = array();
			foreach ($buildingIds as $buildingId) {
				$building = new Building();
				$building->buildingId = $buildingId;
				$building->populate();
				$buildings[] = $building->displayName;
			}
			$eventBuilding = implode(', ',$buildings);
			$data['header'] .= '<br>('.$eventBuilding.')';
			$title .= ' -> ('.$eventBuilding.')';
		}
		$data['header'] = '<label title="'.$title.'">'.$data['header'].'</label>';
		return $data;
	}

	public function ajaxGetColumnHeaderAction() {
		$columnIndex = $this->_getParam('columnIndex');
		$filter = $this->getCurrentDisplayFilter($columnIndex);
		if (!isset($filter->columns[$columnIndex])) {
			throw new Exception(__("Cannot generate column with that index, there is no filter defined for that column Index: ") . $columnIndex);
		}

		$data = $this->_generateColumnHeader($filter->columns[$columnIndex]);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

    public function viewDayAction() {
	$this->view->columns = $this->getCurrentDisplayFilter()->columns;
	$this->render('view-day');
    }

	public function setFilterAction() {
		$id = $this->_getParam('id');
		$filter = $this->getCurrentDisplayFilter($id);
		if (!isset($filter->columns[$id])) {
			throw new Exception(__("Cannot set filter column with that index, there is no filter defined for that column Index: ") . $id);
		}
		$column = $filter->columns[$id];
		$this->view->columnId = $id;
		$this->view->data = $column;
		$this->render('set-filter');
	}

	public function processSetFilterAction() {
		$calendar = $this->_getParam('calendar');
		$id = $calendar['columnId'];
		$filter = $this->getCurrentDisplayFilter($id);
		if (!isset($filter->columns[$id])) {
			throw new Exception(__("Cannot set filter column with that index, there is no filter defined for that column Index: ") . $id);
		}

		$filterState =  new FilterState();
		if ($id > 0) {
			$filterState->filterStateId = $id;
			$filterState->populate();
		}
		if (!isset($calendar['tabName'])) $filterState->tabName = self::TAB_NAME;
		$filterState->populateWithArray($calendar);
		$filterState->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$filterState->persist();
		$filterStateId = (int)$filterState->filterStateId;
		$data = array('id'=>$filterStateId);
		$this->_session->filter->columns[$filterStateId] = $filterState->toArray();

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxUpdateAppointmentAction() {
		$idFrom = $this->_getParam('idFrom');

		$columnIdFrom = $this->_getParam('columnIdFrom');
		$columnIdTo = $this->_getParam('columnIdTo');
		$isCopy = $this->_getParam('isCopy');
		$timeTo = $this->_getParam('timeTo');

		$filter = $this->getCurrentDisplayFilter(array($columnIdFrom,$columnIdTo));
		$columns = $filter->columns;
		if (!isset($columns[$columnIdFrom])) {
			throw new Exception(__("Cannot generate SOURCE column with that index, there is no filter defined for that column Index: ") . $columnIdFrom);
		}
		if (!isset($columns[$columnIdTo])) {
			throw new Exception(__("Cannot generate TO column with that index, there is no filter defined for that column Index: ") . $columnIdTo);
		}

		$columnFrom = $columns[$columnIdFrom];
		$columnTo = $columns[$columnIdTo];

		$providerIdFrom = $columnFrom['providerId'];
		$providerIdTo = $columnTo['providerId'];
		$roomIdTo = $columnTo['roomId'];

		$app = new Appointment();
		$app->appointmentId = (int)$idFrom;
		$data = array(
			'error'=>'Appointment does not exists',
		);
		if ($app->populate()) {
			$data = array();
			$startDate = isset($columnTo['dateFilter'])?$columnTo['dateFilter']:$filter->date;

			$startTime = strtotime($app->start);
			$endTime = strtotime($app->end);
			$diffTime = $endTime - $startTime;

			$newStartTime = strtotime($startDate . ' ' . $timeTo);
			$newEndTime = $newStartTime + $diffTime;

			$app->start = date('Y-m-d H:i:s', $newStartTime);
			$app->end = date('Y-m-d H:i:s', $newEndTime);
			$data['timeTo'] = $timeTo;

			$app->lastChangeDate = date('Y-m-d H:i:s');
			$app->providerId = $providerIdTo;
			$app->roomId = $roomIdTo;
			if (strtolower($isCopy) == 'true') {
				// zero out appointmentId to act a new copy
				$app->appointmentId = 0;
			}
			$forced = (int)$this->_getParam('forced');
			if (!$forced && $error = $app->checkRules()) { // prompt the user if the appointment being made would be a double book or is outside of schedule time.
				$data['confirmation'] = $error;
			}
			else {
				$app->persist();
				//trigger_error(print_r($params,true));
				$data['appointmentId'] = $app->appointmentId;
			}
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$data['columnIdFrom'] = $columnIdFrom;
		$data['columnIdTo'] = $columnIdTo;
		$json->direct($data);
	}

	public function ajaxGetMenuAction() {
		$menus = array();
		$menus[] = array('text'=>__('Add Column'), 'id'=>'add_column');
		$menus[] = array('text'=>__('Remove This Column'), 'id'=>'remove_column');
		$menus[] = array('text'=>__('Select Date'), 'id'=>'select_date');
		$menus[] = array('text'=>__('Edit This Appointment'), 'id'=>'edit_appointment');
		$menus[] = array('text'=>__('Create Visit'), 'id'=>'create_visit');
		$menus[] = array('text'=>__('Add Payment'), 'id'=>'add_payment');
		$menus[] = array('text'=>__('Cancel Move'), 'id'=>'cancel_move');
		$menus[] = array('text'=>__('Find First'), 'id'=>'find_first');
		$menus[] = array('text'=>__('Time Search'), 'id'=>'timeSearch');
		$this->view->menus =  $menus;
		$this->view->stations = Enumeration::getEnumArray(Routing::ENUM_PARENT_NAME);
		header('Content-Type: application/xml;');
		$this->render('ajax-get-menu');
	}

	public function ajaxRemoveColumnAction() {
		$id = $this->_getParam('id');
		$filter = $this->getCurrentDisplayFilter($id);
		$columns = $filter->columns;
		if (!isset($columns[$id])) {
			throw new Exception(__("Cannot generate column with that index, there is no filter defined for that column Index: ") . $id);
		}
		$filterState = new FilterState();
		if (isset($columns[$id]['filterStateId'])) {
			$filterStateId = $columns[$id]['filterStateId'];
			$filterState->deleteByFilterStateId($filterStateId);
		}
		unset($this->_session->filter->columns[$id]);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$data = array();
		$data['ret'] = true;
		$json->direct($data);
	}

	public function addColumnAction() {
		$this->render('add-column');
	}

	public function processAddColumnAction() {
		$calendar = $this->_getParam('calendar');

		$filter = $this->getCurrentDisplayFilter();
		$providerId = $calendar['providerId'];
		$roomId = $calendar['roomId'];

		$filterState =  new FilterState();
		if (!isset($calendar['tabName'])) $filterState->tabName = self::TAB_NAME;
		$filterState->populateWithArray($calendar);
		$filterState->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$filterState->persist();

		$this->_session->filter->columns[$filterState->filterStateId] = $filterState->toArray();
		$data = array();

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$data['providerId'] = $providerId;
		$data['columnId'] = max(array_keys($this->_session->filter->columns));
		$json->direct($data);
	}

	public function addAppointmentAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$columnId = $this->_getParam('columnId');
		$appointment = new Appointment();
		if ($appointmentId > 0) {
			$filter = $this->getCurrentDisplayFilter($columnId);
			$appointment->appointmentId = (int)$appointmentId;
			$appointment->populate();
			$date = date('Y-m-d', strtotime($appointment->start));
			$appointment->start = date('H:i', strtotime($appointment->start));
			$appointment->end = date('H:i', strtotime($appointment->end));
			$providerId = (int)$appointment->providerId;
			$roomId = (int)$appointment->roomId;
			foreach ($filter->columns as $index=>$col) {
				if ($col['dateFilter'] == $date && (int)$col['providerId'] == $providerId && (int)$col['roomId'] == $roomId) {
					$this->view->columnId = $index;
					break;
				}
			}
			$recordNumber = $appointment->patient->record_number;
			$lastName = $appointment->patient->last_name;
			$firstName = $appointment->patient->first_name;
			$middleInitial = '';
			if (strlen($appointment->patient->middle_name) > 0) {
				$middleInitial = $appointment->patient->middle_name{0};
			}
			$this->view->patient = "{$lastName}, {$firstName} {$middleInitial} #{$recordNumber} PID:{$appointment->patient->person_id}";
		}
		else {
			$rowId = $this->_getParam('rowId');
			$start = $this->_getParam('start');
			if (strlen($columnId) > 0) {
				$this->view->columnId = $columnId;
				$filter = $this->getCurrentDisplayFilter($columnId);
				if (!isset($filter->columns[$columnId])) {
					throw new Exception(__("Cannot generate column with that index, there is no filter defined for that column Index: ") . $columnId);
				}
				$column = $filter->columns[$columnId];
				$appointment->providerId = (isset($column['providerId'])) ? $column['providerId'] : 0;
				$appointment->roomId = (isset($column['roomId'])) ? $column['roomId'] : 0;
			}
			if (strlen($start) > 0) {
				$appointment->start = $start;
				$appointment->end = date('H:i', strtotime('+'.self::FILTER_MINUTES_INTERVAL.' minutes', strtotime($start)));
			}
		}

		$form = new WebVista_Form(array('name' => 'add-appointment'));
		$form->setAction(Zend_Registry::get('baseUrl') . "calendar.raw/process-add-appointment");
		$form->loadORM($appointment, "Appointment");
		$form->setWindow('windowAppointmentId');
		$this->view->form = $form;

		$this->view->reasons = PatientNote::listReasons();

		$phones = array();
		$phone = new PhoneNumber();
		$phoneIterator = array();
		if ($appointment->patientId > 0) $phoneIterator = $phone->getIteratorByPersonId($appointment->patientId);
		foreach ($phoneIterator as $row) {
			$phones[] = $row->number;
		}
		$this->view->phones = $phones;

		$appointmentTemplate = new AppointmentTemplate();
		$this->view->appointmentReasons = $appointmentTemplate->getAppointmentReasons();

		$this->view->appointment = $appointment;
		$this->render('add-appointment');
	}

	public function processAddAppointmentAction() {
		$appointment = $this->_getParam('appointment');
		$paramProviders = array();
		foreach ($appointment as $key=>$val) {
			$providerPrefix = 'providerId-';
			if (substr($key,0,strlen($providerPrefix)) == $providerPrefix) {
				$paramProviders[] = $val;
				unset($appointment[$key]);
			}
		}
		if (count($paramProviders) > 0) {
			// assign the first providerId
			$appointment['providerId'] = array_shift($paramProviders);
		}
		// extra providers if any, can be retrieved using $paramProviders variable, not sure where to place it
		$columnId = $this->_getParam('columnId');
		$rowId = $this->_getParam('rowId');
		$forced = (int)$this->_getParam('forced');
		$filter = $this->getCurrentDisplayFilter($columnId);

		if (!isset($filter->columns[$columnId])) {
			throw new Exception(__("Cannot generate column with that index, there is no filter defined for that column Index: ") . $columnId);
		}

		$column = $filter->columns[$columnId];
		$dateFilter = isset($column['dateFilter'])?$column['dateFilter']:$filter->date;

		$data = array();
		$data['columnId'] = $columnId;
		$data['rowId'] = $rowId;
		$roomId = isset($column['roomId'])?(int)$column['roomId']:0;
		if ($roomId > 0) $appointment['roomId'] = $roomId;

		$app = new Appointment();
		if (isset($appointment['appointmentId']) && $appointment['appointmentId'] > 0) {
			$app->appointmentId = (int)$appointment['appointmentId'];
			$app->populate();
		}
		$app->populateWithArray($appointment);
		if ($app->appointmentId > 0) {
			$app->lastChangeId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$app->lastChangeDate = date('Y-m-d H:i:s');
		}
		else {
			$app->creatorId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$app->createdDate = date('Y-m-d H:i:s');
		}
		//$app->providerId = $appointment['providerId'];

		//$app->patientId = substr($appointment['patient'],stripos($appointment['patient'],'PID:') + 4);
		$app->walkin = isset($appointment['walkin'])?1:0;
		$app->start = $dateFilter . ' ' . date('H:i:s', strtotime($appointment['start']));
		$app->end = $dateFilter . ' ' . date('H:i:s', strtotime($appointment['end']));

		if (!$forced && $error = $app->checkRules()) { // prompt the user if the appointment being made would be a double book or is outside of schedule time.
			$data['error'] = $error;
		}
		else {
			$app->persist();
			$data['appointmentId'] = $app->appointmentId;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxSetFilterDayAction() {
		$day = $this->_getParam('day');
		$filter = $this->getCurrentDisplayFilter();
		$time = 0;
		if ($day == 'next') {
			$time = '+1 day';
			$date = strtotime($time,strtotime($filter->date));
		}
		else if ($day == 'previous') {
			$time = '-1 day';
			$date = strtotime($time,strtotime($filter->date));
		}
		else {
			$x = explode('-',$day);
			$m = $x[1];
			$d = $x[2];
			$y = $x[0];
			if (count($x) != 3 || !checkdate($m,$d,$y)) {
				$msg = 'Invalid date format!';
				throw new Exception($msg);
			}
			$date = strtotime($day);
		}
		$this->_session->filter->date = date('Y-m-d',$date);
		$columns = $this->_session->filter->columns;
		foreach ($columns as $index=>$col) {
			if (!isset($col['dateFilter'])) continue;
			if ($time === 0) {
				$tmpDate = $this->_session->filter->date;
			}
			else {
				$tmpDate = date('Y-m-d',strtotime($time,strtotime($col['dateFilter'])));
			}
			$this->_session->filter->columns[$index]['dateFilter'] = $tmpDate;
			$fs = $this->_session->filter->columns[$index];
			if (isset($fs['filterStateId'])) {
				$filterState = new FilterState();
				$filterState->filterStateId = (int)$fs['filterStateId'];
				$filterState->populate();
				$filterState->populateWithArray($fs);
				$filterState->persist();
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$data = array();
		$json->direct($data);
	}

	public function posContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('pos-context-menu');
	}

	protected function _generateEligibilityRowData(InsuredRelationship $insuredRelationship) {
		$row = array();
		$row['id'] = $insuredRelationship->insuredRelationshipId;
		$row['data'] = array();
		$row['data'][] = $insuredRelationship->displayDateLastVerified;
		$row['data'][] = $insuredRelationship->displayProgram;
		$expires = explode(':',$insuredRelationship->displayExpires);
		$row['data'][] = $expires[0]; // expires
		$row['data'][] = $insuredRelationship->displayVerified;
		$row['data'][] = $insuredRelationship->desc;
		$row['data'][] = $expires[1]; // color
		return $row;
	}

	public function processUpdateEligibilityAction() {
		$id = (int)$this->_getParam('id');
		$data = false;
		if ($id > 0) {
			$insuredRelationship = new InsuredRelationship();
			$insuredRelationship->insuredRelationshipId = $id;
			$insuredRelationship->populate();
		}
		$params = $this->_getParam('pos');
		$insuredRelationship->populateWithArray($params);
		$insuredRelationship->persist();
		$data = array('row'=>$this->_generateEligibilityRowData($insuredRelationship));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processCheckEligibilityAction() {
		$id = (int)$this->_getParam('id');
		$data = false;
		if ($id > 0) {
			$insuredRelationship = InsuredRelationship::eligibilityCheck($id);
			$data = array('row'=>$this->_generateEligibilityRowData($insuredRelationship));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function pointOfSaleAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$appointment->populate();
		$this->view->appointment = $appointment;
		$visit = new Visit();
		$visit->appointmentId = $appointmentId;
		$visit->populateByAppointmentId();
		$this->view->visitId = (int)$visit->visitId;
		$this->render('point-of-sale');
	}

	protected function _generatePaymentRowData(Payment $payment) {
		$row = array();
		$row['id'] = $payment->paymentId;
		$row['data'] = array();
		$row['data'][] = date('Y-m-d',strtotime($payment->paymentDate));
		$row['data'][] = $payment->paymentType;
		$row['data'][] = $payment->amount;
		$row['data'][] = $payment->title;
		return $row;
	}

	public function listPaymentsAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$rows = array();
		$payment = new Payment();
		foreach ($payment->getIteratorByAppointmentId($appointmentId) as $row) {
			$rows[] = $this->_generatePaymentRowData($row);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	protected function _generateMiscChargeRowData(MiscCharge $charge) {
		$row = array();
		$row['id'] = $charge->miscChargeId;
		$row['data'] = array();
		$row['data'][] = date('Y-m-d',strtotime($charge->chargeDate));
		$row['data'][] = $charge->chargeType;
		$row['data'][] = $charge->amount;
		$row['data'][] = $charge->note;
		return $row;
	}

	public function listChargesAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$rows = array();
		$miscCharge = new MiscCharge();
		foreach ($miscCharge->getIteratorByAppointmentId($appointmentId) as $row) {
			$rows[] = $this->_generateMiscChargeRowData($row);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listEligibilityAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$rows = array();

		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$appointment->populate();
		$personId = (int)$appointment->patientId;

		$insuredRelationship = new InsuredRelationship();
		$insuredRelationship->personId = $personId;
		$insuredRelationshipIterator = $insuredRelationship->getActiveEligibility();
		foreach ($insuredRelationshipIterator as $item) {
			$rows[] = $this->_generateEligibilityRowData($item);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processAddPaymentAction() {
		$params = $this->_getParam('app');
		if (!isset($params['paymentDate'])) $params['paymentDate'] = date('Y-m-d');
		$payment = new Payment();
		$payment->timestamp = date('Y-m-d H:i:s');
		$this->_processEditPaymentCharge($payment,$params);
	}

	public function processEditPaymentAction() {
		$params = $this->_getParam('app');
		$payment = new Payment();
		$paymentId = 0;
		if (isset($params['id'])) {
			$paymentId = (int)$params['id'];
			unset($params['id']);
		}
		if ($paymentId > 0) {
			$payment->paymentId = $paymentId;
			$payment->populate();
		}
		$this->_processEditPaymentCharge($payment,$params);
	}

	public function processAddChargeAction() {
		$params = $this->_getParam('app');
		if (!isset($params['chargeDate'])) $params['chargeDate'] = date('Y-m-d');
		$this->_processEditPaymentCharge(new MiscCharge(),$params);
	}

	public function processEditChargeAction() {
		$params = $this->_getParam('app');
		$charge = new MiscCharge();
		$chargeId = 0;
		if (isset($params['id'])) {
			$chargeId = (int)$params['id'];
			unset($params['id']);
		}
		if ($chargeId > 0) {
			$charge->miscChargeId = $chargeId;
			$charge->populate();
		}
		$this->_processEditPaymentCharge($charge,$params);
	}

	protected function _processEditPaymentCharge($obj,$params=null) {
		if ($params === null) $params = $this->_getParam('app');
		$obj->populateWithArray($params);
		$obj->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$method = '_generate'.get_class($obj).'RowData';
		$json->direct($this->$method($obj));
	}

	protected function getCurrentDisplayFilter($ids=null) {
		//unset($this->_session->filter);
		$date = date('Y-m-d');
		$placeHolder = array('providerId'=>'placeHolderId','roomId'=>0,'dateFilter'=>$date);
		if (isset($this->_session->filter)) {
			$columns = $this->_session->filter->columns;
			$ctr = count($columns);
			if ($ctr > 1) {
				if (isset($columns[0])) {
					unset($this->_session->filter->columns[0]); // remove placeholder column
					unset($columns[0]);
				}
				if (!is_array($ids)) $ids = array($ids);
				foreach ($ids as $id) {
					$id = (int)$id;
					if ($id > 0 && !isset($this->_session->filter->columns[$id])) {
						$filterState = new FilterState();
						$filterState->filterStateId = $id;
						$filterState->populate();
						$this->_session->filter->columns[$id] = $filterState->toArray();
					}
				}
			}
			else {
				if ($ids !== null && $ids == 0 && !isset($columns[$ids])) $this->_session->filter->columns[$ids] = $placeHolder;
			}
			return $this->_session->filter;
		}
		$filter = new StdClass();
		$filter->date = $date;
		$filter->start = self::FILTER_TIME_START;
		$filter->end = self::FILTER_TIME_END;
		$filter->columns = array();
		// retrieve from database
		$filterStateIterator = new FilterStateIterator();
		$filters = array();
		$filters['tabName'] = self::TAB_NAME;
		$filters['userId'] = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$filterStateIterator->setFilters($filters);
		foreach ($filterStateIterator as $state) {
			$filter->columns[$state->filterStateId] = $state->toArray();
		}
		if (!count($filter->columns) > 0) {
			$filter->columns[] = $placeHolder;
		}
		// save to session
		$this->_session->filter = $filter;
		return $filter;
	}

    protected function generateEventColumnData($columnIndex,$includeHeader=false) {
	$columnIndex = (int) $columnIndex;
        $appointmentIterator = new AppointmentIterator();

	if (!isset($this->getCurrentDisplayFilter($columnIndex)->columns[$columnIndex])) {
		throw new Exception(__("Cannot generate column with that index, there is no filter defined for that column Index: ") . $columnIndex);
	}

	$this->_session->currentAppointments[$columnIndex] = array();
	$filter = $this->getCurrentDisplayFilter();
	$filterTimeStart = strtotime($filter->start);
	$filterTimeEnd = strtotime($filter->end);

	$paramFilters = $filter->columns[$columnIndex];
	if (isset($paramFilters['dateFilter'])) {
		$filter->date = date('Y-m-d',strtotime($paramFilters['dateFilter']));
	}
	$paramFilters['start'] = $filter->date . ' ' . self::FILTER_TIME_START;
	$paramFilters['end'] = $filter->date . ' ' . self::FILTER_TIME_END;

	$columnData = array();
	// we need to get the length of time to create number of rows in the grid
	$timeLen = (($filterTimeEnd - $filterTimeStart) / 60) / self::FILTER_MINUTES_INTERVAL;
	for ($i=0;$i<=$timeLen;$i++) {
		$row = array();
		// assign row id as rowNumber and columnIndex
		$row['id'] = $i.$columnIndex;
		$row['data'][0] = '';
		$columnData[$i] = $row;
	}

	$filterToTimeStart = strtotime($paramFilters['start']);
	$appointmentIterator->setFilter($paramFilters);
	// hold the temporary data counter
	$tmpDataCtr = array();
	$colMultiplier = 1;
	$zIndex = 0;
	$refHeight = 22;
	foreach ($appointmentIterator as $row) {
		$startToTime = strtotime($row->start);
		$endToTime = strtotime($row->end);
		$tmpStart = date('H:i', $startToTime);
		$tmpEnd = date('H:i', $endToTime);
		$tmpLen = (($endToTime - $startToTime) / 60) / self::FILTER_MINUTES_INTERVAL;
		$timeLen = ceil($tmpLen);
		$tmpIdx = (($startToTime - $filterToTimeStart) / 60) / self::FILTER_MINUTES_INTERVAL;
		$tmpIndex = ceil($tmpIdx);

		if (!isset($columnData[$tmpIndex])) break;

		$height = $refHeight * $timeLen;
		$heightDiff = 0;
		if ($tmpLen != $timeLen) {
			$diff = $refHeight * ($timeLen - $tmpLen);
			$heightDiff = $refHeight - $diff;
			$height -= $heightDiff;
		}
		$marginLeft = 8;
		$multiplier = 1;

		$marginTop = -11.9;
		if ($tmpIdx != $tmpIndex) {
			$marginTop -= $refHeight * ($tmpIndex - $tmpIdx);
			$height -= $refHeight;
			$timeLen--;
			$height += ($heightDiff * 2);
		}

		// check for appointment intersection
		foreach ($tmpDataCtr as $keyCtr=>$dataCtr) {
			if ($startToTime >= $dataCtr['ranges']['start'] && $startToTime < $dataCtr['ranges']['end']) {
				if ($endToTime > $dataCtr['ranges']['end']) $tmpDataCtr[$keyCtr]['ranges']['end'] = $endToTime;
				$tmpDataCtr[$keyCtr]['multiplier']++;
				$multiplier = $tmpDataCtr[$keyCtr]['multiplier'];
				break;
			}
		}
		if ($multiplier === 1) {
			$ranges = array(
				'start'=>$startToTime,
				'end'=>$endToTime,
			);
			$tmpDataCtr[] = array('ranges'=>$ranges,'multiplier'=>$multiplier);
		}
		else {
			$marginLeft = ($multiplier-1) * 250;
		}
		if ($multiplier > $colMultiplier) {
			$colMultiplier = $multiplier;
		}

		$patient = new Patient();
		$patient->setPersonId($row->patientId);
		$patient->populate();
		$person = $patient->person;
		$this->_session->currentAppointments[$columnIndex][$row->appointmentId] = $row;
		$zIndex++;
		$columnData[$tmpIndex]['id'] = $row->appointmentId . 'i' . $columnData[$tmpIndex]['id'];
		$appointmentId = $row->appointmentId;

		$visitIcon = '';
		$visit = new Visit();
		$visit->appointmentId = $appointmentId;
		$visit->populateByAppointmentId();
		if ($visit->visitId > 0) {
			$visitIcon = '<img src="'.$this->view->baseUrl.'/img/appointment_visit.png" alt="'.__('Visit').'" title="'.__('Visit').'" style="border:0px;height:18px;width:18px;margin-left:5px;" />';
		}

		$routingStatuses = array();
		if (strlen($row->appointmentCode) > 0) {
			$routingStatuses[] = __('Mark').': '.$row->appointmentCode;
		}
		$routing = new Routing();
		$routing->personId = $row->patientId;
		$routing->appointmentId = $row->appointmentId;
		$routing->providerId = $row->providerId;
		$routing->roomId = $row->roomId;
		$routing->populateByAppointments();
		if (strlen($routing->stationId) > 0) {
			$routingStatuses[] = __('Station').': '.$routing->stationId;
		}
		$routingStatus = implode(' ',$routingStatuses);
		$nameLink = '';
		if ($row->patientId > 0) {
			$nameLink = "<a href=\"javascript:showPatientDetails({$row->patientId});\">{$person->last_name}, {$person->first_name} (#{$patient->recordNumber})</a>";
		}
		$columnIndex = (int)$columnIndex;
		$divContent = '<div';
		$divContent .= ' id="event'.$appointmentId.'"';
		$divContent .= ' class="dataForeground"';
		$divContent .= ' appointmentId="'.(int)$row->appointmentId.'" visitId="'.(int)$visit->visitId.'"';
		$divContent .= ' style="float:left;position:absolute;margin-top:'.$marginTop.'px;height:'.$height.'px;width:230px;overflow:hidden;border:thin solid black;margin-left:'.$marginLeft.'px;padding-left:2px;background-color:lightgrey;z-index:'.$zIndex.';"';
		$divContent .= ' onmouseover="appointmentMouseOver('.$appointmentId.',this,'.$height.');"';
		$divContent .= ' onmouseout="appointmentMouseOut('.$appointmentId.',this,'.$height.','.$zIndex.');"';
		$divContent .= ' ondblclick="appointmentDoubleClicked(event,'.$appointmentId.')"';
		$divContent .= ' oncontextmenu="appointmentClicked(this,event,'.$columnIndex.');return false;"';
		$divContent .= ' onmousedown="appointmentMouseDown(this,event,'.$columnIndex.')"';
		$divContent .= '>';
		$divContent .= $tmpStart.'-'.$tmpEnd.' '.$nameLink.' '.$visitIcon.' <br />'.$routingStatus.'<div class="bottomInner" id="bottomInnerId'.$appointmentId.'" style="white-space:normal;">'.$row->title.'</div>';

		$divContent .= '</div>';

		$columnData[$tmpIndex]['data'][0] .= $divContent;
		$columnData[$tmpIndex]['userdata']['visitId'] = $visit->visitId;
		$columnData[$tmpIndex]['userdata']['appointmentId'] = $row->appointmentId;
		$columnData[$tmpIndex]['userdata']['length'] = $timeLen;
		if (!isset($columnData[$tmpIndex]['userdata']['ctr'])) {
			$columnData[$tmpIndex]['userdata']['ctr'] = 0;
		}
		$columnData[$tmpIndex]['userdata']['ctr']++;
	}
	$columnData[0]['userdata']['colMultiplier'] = $colMultiplier;
	$columnData[0]['userdata']['providerId'] = $paramFilters['providerId'];
	$roomId = 0;
	if (isset($paramFilters['roomId'])) {
		$roomId = $paramFilters['roomId'];
	}
	$columnData[0]['userdata']['roomId'] = $roomId;

	if ($includeHeader) {
		$header = "{$paramFilters['dateFilter']}<br>";
		$title = $paramFilters['dateFilter'];
		// temporarily set the header as providerId
		$providerId = (int)$paramFilters['providerId'];
		if ($providerId > 0) {
			$provider = new Provider();
			$provider->setPersonId($providerId);
			$provider->populate();
			$name = $provider->optionName;
			// we simply replace the comma with its html equivalent (&#44;) because this may cause not to render the header
			$header .= str_replace(',','&#44;',$name);
			$title .= ' -> '.$name;
		}
		if ($roomId > 0) {
			$room = new Room();
			$room->id = $roomId;
			$room->populate();
			if ($providerId > 0) {
				$header .= '<br>';
			}
			$header .= $room->name;
			$title .= ' -> '.$room->name;
		}
	}

	$buildings = array();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('scheduleEvents',array('start','end','roomId','buildingId','title'))
				->joinLeft('provider','scheduleEvents.providerId=provider.person_id',array('providerColor'=>'color'))
				->joinLeft('person','person.person_id=provider.person_id',array('providerName'=>'CONCAT(person.last_name,\', \',person.first_name,\' \',person.middle_name)'))
				->where('start <= end')
				->order('start ASC');
		if ((int)$paramFilters['roomId'] > 0 && (int)$paramFilters['providerId'] > 0) {
			$sqlSelect->joinLeft('buildings','scheduleEvents.buildingId=buildings.id',null)
				->joinLeft('rooms','rooms.building_id=buildings.id',array('roomColor'=>'color'))
				->where('rooms.id = ?',(int)$paramFilters['roomId'])
				->where('providerId = ?',(int)$paramFilters['providerId']);
		}
		else {
			$sqlSelect->joinLeft('rooms','scheduleEvents.roomId=rooms.id',array('roomColor'=>'color'))
				->where('roomId = ?',(int)$paramFilters['roomId'])
				->where('providerId = ?',(int)$paramFilters['providerId']);
		}
		if (isset($paramFilters['start'])) $sqlSelect->where('start >= ?',$paramFilters['start']);
		if (isset($paramFilters['end'])) $sqlSelect->where('end <= ?',$paramFilters['end']);
		$stmt = $db->query($sqlSelect);
		$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		while ($event = $stmt->fetch()) {
			$x = explode(' ', $event['start']);
			$eventTimeStart = strtotime($x[1]);
			$x = explode(' ', $event['end']);
			$eventTimeEnd = strtotime($x[1]);
			// get the starting index
			$index = (($eventTimeStart - $filterTimeStart) / 60) / self::FILTER_MINUTES_INTERVAL;
			$tmpIndex = $index;
			$color = $event['providerColor'];
			if ($event['roomId'] > 0 && strlen($event['roomColor']) > 0) {
				$color = $event['roomColor'];
			}
			if (substr($color,0,1) != '#') {
				$color = '#'.$color;
			}
			if (!isset($buildings[$event['buildingId']])) {
				$building = new Building();
				$building->buildingId = $event['buildingId'];
				$building->populate();
				$buildings[$building->buildingId] = $building;
			}
			$columnData[$tmpIndex]['data'][0] = '<div style="overflow:hidden;white-space:nowrap;margin-top:-6px;">'.$event['title'].' - '.$buildings[$event['buildingId']]->displayName.'</div>'.$columnData[$tmpIndex]['data'][0];
			while ($eventTimeStart < $eventTimeEnd) {
				$eventDateTimeStart = date('Y-m-d H:i:s',$eventTimeStart);
				$eventTimeStart = strtotime('+'.self::FILTER_MINUTES_INTERVAL.' minutes',$eventTimeStart);
				$columnData[$tmpIndex]['style'] = 'background-color:'.$color.';border-color:lightgrey;';
				$columnData[$tmpIndex]['userdata']['title'] = $event['title'].' of '.$event['providerName'].' ('.$buildings[$event['buildingId']]->displayName.')';
				$tmpIndex++;
			}
		}

		$ret = array('rows'=>$columnData);
		if ($includeHeader) {
			$tmp = array();
			foreach ($buildings as $building) {
				$tmp[] = $building->displayName;
			}
			$eventBuilding = implode(', ',$tmp);
			$header .= '<br>('.$eventBuilding.')';
			$title .= ' -> ('.$eventBuilding.')';

			$header = '<label title="'.$title.'">'.$header.'</label>';
		        $ret = array(
				'header'=>$header,
				'events'=>$ret,
			);
		}
        	return $ret;
    }

    protected function generateTimeColumnData() {
        $filter = $this->getCurrentDisplayFilter();
        $data = array();
        $timeStart = strtotime("{$filter->date} {$filter->start}");
        $timeEnd = strtotime("{$filter->date} {$filter->end}");
        while ($timeStart <= $timeEnd) {
            $tmp = array();
            $tmp['id'] = $timeStart;
            $tmp['data'][] = date('H:i',$timeStart);
            $data[] = $tmp;
            $timeStart = strtotime('+'.self::FILTER_MINUTES_INTERVAL.' minutes',$timeStart);
        }
	$data[0]['userdata']['numberOfRows'] = count($data);
        $ret = array();
        $ret['rows'] = $data;
        return $ret;
    }

	public function timeSearchAction() {
		$columnId = (int)$this->_getParam('columnId');
		$filter = $this->getCurrentDisplayFilter($columnId);
		$columns = $filter->columns;
		if (!isset($columns[$columnId])) {
			throw new Exception(__('There is no filter defined for column Index: ').$columnId);
		}
		$column = $columns[$columnId];
		$this->view->providerId = (int)$column['providerId'];
		$this->view->roomId = (int)$column['roomId'];
		$dateFilter = $column['dateFilter'];
		if (!strlen($dateFilter) > 0 || $dateFilter == '0000-00-00') {
			$dateFilter = date('Y-m-d');
		}

		$providers = array(0=>'');
		$provider = new Provider();
		foreach ($provider->getIter() as $row) {
			$providers[$row->providerId] = $row->optionName;
		}
		$this->view->providers = $providers;
		$rooms = array(0=>'');
		foreach (Room::getRoomArray() as $key=>$value) {
			$rooms[$key] = $value;
		}
		$this->view->rooms = $rooms;

		$dateStart = date('Y-m-d',strtotime('+1 day',strtotime($dateFilter)));
		$startToTime = strtotime($dateStart);
		$months = array();
		for ($i=0;$i<4;$i++) {
			$months[] = array(
				'month'=>date('Y-m',$startToTime),
				'jsmonth'=>date('Y',$startToTime).((int)date('m',$startToTime)-1),
				'lastDay'=>date('t',$startToTime),
			);
			$startToTime = strtotime('+1 month',$startToTime);
		}
		$dateEnd = $months[3]['month'].'-'.$months[3]['lastDay'];
		$this->view->months = $months;
		$this->view->dateStart = $dateStart;
		$this->view->dateEnd = $dateEnd;
		$this->view->timeStart = '00:00';
		$this->view->timeEnd = '23:59';

		$this->render();
	}

	public function lookupTimeSearchAction() {
		calcTS();
		trigger_error('before free time search: '.calcTS(),E_USER_NOTICE);
		$dateStart = date('Y-m-d H:i:s',strtotime($this->_getParam('dateStart')));
		$dateEnd = date('Y-m-d H:i:s',strtotime($this->_getParam('dateEnd')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$scheduleEvent = new ScheduleEvent();
		$scheduleEvent->start = $dateStart;
		$scheduleEvent->end = $dateEnd;
		$scheduleEvent->providerId = $providerId;
		$scheduleEvent->roomId = $roomId;
		//file_put_contents('/tmp/schedule.txt','');
		$data = $scheduleEvent->bookingAppointmentDetails();
		$ret = array();
		foreach($data as $date=>$value) {
			$x = explode('-',$date);
			if (!isset($x[2])) {
				$ret[$date] = $value;
				continue;
			}
			$x[1] -= 1; // month - 1
			$year = (int)$x[0];
			$month = (int)$x[1];
			$day = (int)$x[2];
			$ymd = $year.$month.$day;
			$ret[$ymd] = array(
				'month'=>$year.$month,
				'value'=>$value,
			);
		}
		trigger_error('after free time search: '.calcTS(),E_USER_NOTICE);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function getAppointmentDetailsAction() {
		$dateFilter = date('Y-m-d',strtotime($this->_getParam('date')));
		$providerId = (int)$this->_getParam('providerId');
		$roomId = (int)$this->_getParam('roomId');
		$filters = array(
			'dateFilter'=>$dateFilter,
			'providerId'=>$providerId,
			'roomId'=>$roomId,
		);
		$data = $this->_generateEventColumnData($filters);

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateEventColumnData(Array $filters) { // filters assume to be sanitized
		$appointment = new Appointment();
		$appointment->providerId = $filters['providerId'];
		$appointment->roomId = $filters['roomId'];
		$appointment->start = $filters['dateFilter'].' '.self::FILTER_TIME_START;
		$appointment->end = $filters['dateFilter'].' '.self::FILTER_TIME_END;

		$startTime = strtotime($appointment->start);
		$endTime = strtotime($appointment->end);
		// we need to get the length of time to create number of rows in the grid
		$timeLen = (($endTime - $startTime) / 60) / self::FILTER_MINUTES_INTERVAL;
		// NOTE: height is 22px for xp, default 20px
		// prepopulate return data
		$columnData = array();
		//$maps = array();
		$start = $startTime;
		$end = $endTime;
		$ctr = 0;
		while ($start <= $end) {
			$map = array();
			$map['start'] = $start;
			$start = strtotime('+'.self::FILTER_MINUTES_INTERVAL.' minutes',$start);
			$map['end'] = $start;
			//$maps[$ctr] = $map;

			$row = array();
			// assign row id as rowNumber and columnIndex
			$columnData[$ctr] = array(
				'id'=>($ctr+1),
				'data'=>array(''),
				'map'=>$map,
			);
			$ctr++;
		}
		$columnDataCtr = count($columnData);

		$data = array(
			'apps'=>array(),
			'events'=>array(),
		);

		$mapIndex = 0;
		$apps = array();
		foreach ($appointment->getIter() as $app) {
			$start = strtotime($app->start);
			for ($i=$mapIndex;$i<$columnDataCtr;$i++) {
				$map = $columnData[$mapIndex]['map'];
				$mapIndex = $i;
				if ($start >= $map['start'] && $start <= $map['end']) {
					if (!isset($data['apps'][$i])) $data['apps'][$i] = array();
					$data['apps'][$i][] = $app;
					break;
				}
			}
		}

		$mapIndex = 0;
		$events = array();
		trigger_error('looping schedule event started');

		$scheduleEvent = new ScheduleEvent();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('scheduleEvents')
				->where('providerId = ?',$appointment->providerId)
				->where('roomId = ?',$appointment->roomId)
				->where('start >= ?', $appointment->start)
				->where('end <= ?',$appointment->end)
				->where('start <= end')
				->order('start ASC');
		$stmt = $db->query($sqlSelect);
		$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		while ($row = $stmt->fetch()) {
			$event = new ScheduleEvent();
			$event->populateWithArray($row);
			$start = strtotime($event->start);
			for ($i=$mapIndex;$i<$columnDataCtr;$i++) {
				$map = $columnData[$i]['map'];
				$mapIndex = $i;
				if ($start >= $map['start'] && $start <= $map['end']) {
					if (!isset($data['events'][$i])) $data['events'][$i] = array();
					$data['events'][$i] = $event;
					break;
				}
			}
		}
		trigger_error('looping schedule event ended');

		$colMultiplier = 1;
		$zIndex = 0;
		foreach ($data['apps'] as $index=>$apps) {
			$ctr = count($apps);
			$columnData[$index]['userdata']['ctr'] = $ctr;
			if ($ctr > $colMultiplier) $colMultiplier = $ctr;
			for ($i=0;$i<$ctr;$i++) {
				$app = $apps[$i];
				$this->_populateAppointmentRow($app,$columnData,$index,$i,$ctr);
			}
		}

		$header = "{$filters['dateFilter']}<br>";
		$title = $filters['dateFilter'];
		// temporarily set the header as providerId
		$providerId = (int)$filters['providerId'];
		$roomId = (int)$filters['roomId'];
		if ($providerId > 0) {
			$provider = new Provider();
			$provider->setPersonId($providerId);
			$provider->populate();
			$name = $provider->optionName;
			// we simply replace the comma with its html equivalent (&#44;) because this may cause not to render the header
			$header .= str_replace(',','&#44;',$name);
			$title .= ' -> '.$name;
		}
		if ($roomId > 0) {
			$room = new Room();
			$room->id = $roomId;
			$room->populate();
			if ($providerId > 0) {
				$header .= '<br>';
			}
			$header .= $room->name;
			$title .= ' -> '.$room->name;
		}

		$buildings = array();
		foreach ($data['events'] as $index=>$event) {
			$this->_populateScheduleEventRow($event,$columnData,$index);
			$buildingId = (int)$event->buildingId;
			$building = new Building();
			$building->buildingId = $buildingId;
			$building->populate();
			$buildings[$buildingId] = $building->displayName;
		}
		$eventBuilding = implode(', ',$buildings);
		$header .= '<br>('.$eventBuilding.')';
		$title .= ' -> ('.$eventBuilding.')';

		$header = '<label title="'.$title.'">'.$header.'</label>';

		for ($i=0;$i<$columnDataCtr;$i++) {
			unset($columnData[$i]['map']);
		}

		$columnData[0]['userdata']['colMultiplier'] = $colMultiplier;
		$columnData[0]['userdata']['providerId'] = $appointment->providerId;
		$columnData[0]['userdata']['roomId'] = $appointment->roomId;
		$columnData[0]['userdata']['date'] = $filters['dateFilter'];

		return array(
			'header'=>$header,
			'events'=>array(
				'rows'=>$columnData,
			),
		);
	}

	protected function _populateAppointmentRow(Appointment $app,Array &$columnData,$colIndex,$index,$rowsLen) {
		$startToTime = strtotime($app->start);
		$endToTime = strtotime($app->end);
		$tmpStart = date('H:i', $startToTime);
		$tmpEnd = date('H:i', $endToTime);
		$timeLen = ceil((($endToTime - $startToTime) / 60) / self::FILTER_MINUTES_INTERVAL);

		$appointmentId = (int)$app->appointmentId;
		$patientId = (int)$app->patientId;
		$providerId = (int)$app->providerId;
		$roomId = (int)$app->roomId;

		$patient = new Patient();
		$patient->personId = $patientId;
		$patient->populate();
		$id = isset($columnData[$colIndex]['id'])?$columnData[$colIndex]['id']:'';
		$columnData[$colIndex]['id'] = $appointmentId;
		if (strlen($id) > 0) $columnData[$colIndex]['id'] .= 'i'.$id;

		$visit = new Visit();
		$visit->appointmentId = $appointmentId;
		$visit->populateByAppointmentId();
		$visitIcon = ($visit->visitId > 0)?'<img src="'.$this->view->baseUrl.'/img/appointment_visit.png" alt="'.__('Visit').'" title="'.__('Visit').'" style="border:0px;height:18px;width:18px;margin-left:5px;" />':'';

		$routingStatuses = array();
		if (strlen($app->appointmentCode) > 0) $routingStatuses[] = __('Mark').': '.$app->appointmentCode;

		$routing = new Routing();
		$routing->personId = $patientId;
		$routing->appointmentId = $appointmentId;
		$routing->providerId = $providerId;
		$routing->roomId = $roomId;
		$routing->populateByAppointments();
		if (strlen($routing->stationId) > 0) $routingStatuses[] = __('Station').': '.$routing->stationId;

		$routingStatus = implode(' ',$routingStatuses);
		$nameLink = ($patientId > 0)?"<a href=\"javascript:showPatientDetails({$patientId});\">{$patient->person->displayName} (#{$patient->recordNumber})</a>":'';

		$cellRow = 20;
		$height = $cellRow * $timeLen * 1.1;

		$marginTop = 2;
		// compute and adjust margin top and height
		$heightPerMinute = $cellRow / self::FILTER_MINUTES_INTERVAL;
		$map = $columnData[$colIndex]['map'];
		$diff = ($startToTime - $map['start']) / 60;
		if ($diff > 0) {
			$marginTop += $diff * $heightPerMinute;
			$height -= $marginTop;
		}

		$marginLeft = ($rowsLen > 1 && $index > 0)?($index * 250):8;
		$zIndex = $colIndex.$index;
		$columnData[$colIndex]['data'][0] .= "<div onmousedown=\"calendarSetAppointmentId('$appointmentId')\" ondblclick=\"timeSearchDoubleClicked(this,event)\" appointmentId=\"{$appointmentId}\" visitId=\"{$visit->visitId}\" style=\"float:left;position:absolute;margin-top:{$marginTop}px;height:{$height}px;width:230px;overflow:hidden;border:thin solid black;margin-left:{$marginLeft}px;padding-left:2px;background-color:lightgrey;z-index:{$zIndex};\" class=\"dataForeground\" id=\"event{$appointmentId}\" onmouseover=\"calendarExpandAppointment({$appointmentId},this,{$height});\" onmouseout=\"calendarShrinkAppointment({$appointmentId},this,{$height},{$zIndex});\">{$tmpStart}-{$tmpEnd} {$nameLink} {$visitIcon} <br />{$routingStatus}<div class=\"bottomInner\" id=\"bottomInnerId{$appointmentId}\" style=\"white-space:normal;\">{$app->title}</div></div>";
		$columnData[$colIndex]['userdata']['visitId'] = $visit->visitId;
		$columnData[$colIndex]['userdata']['appointmentId'] = $appointmentId;
		$columnData[$colIndex]['userdata']['length'] = $timeLen;
	}

	protected function _populateScheduleEventRow(ScheduleEvent $event,Array &$columnData,$colIndex) {
		$buildings = array();

		$eventTimeStart = strtotime($event->start);
		$eventTimeEnd = strtotime($event->end);
		// get the starting index
		$tmpIndex = $colIndex;

		$color = $event->provider->color;
		if ($event->roomId > 0 && strlen($event->room->color) > 0) {
			$color = $event->room->color;
		}
		if (substr($color,0,1) != '#') {
			$color = '#'.$color;
		}
		if (!isset($buildings[$event->buildingId])) {
			$building = new Building();
			$building->buildingId = $event->buildingId;
			$building->populate();
			$buildings[$building->buildingId] = $building;
		}
		$columnData[$tmpIndex]['data'][0] = $event->title.' - '.$buildings[$event->buildingId]->displayName.$columnData[$tmpIndex]['data'][0];
		while ($eventTimeStart < $eventTimeEnd) {
			$eventDateTimeStart = date('Y-m-d H:i:s',$eventTimeStart);
			//$eventTimeStart = strtotime('+'.self::FILTER_MINUTES_INTERVAL.' minutes',$eventTimeStart);
			$eventTimeStart += self::FILTER_MINUTES_INTERVAL * 60;
			$columnData[$tmpIndex]['style'] = 'background-color:'.$color.';border-color:lightgrey;';
			$columnData[$tmpIndex]['userdata']['title'] = $event->title.' of '.$event->provider->optionName.' ('.$buildings[$event->buildingId]->displayName.')';
			$tmpIndex++;
		}
	}

	public function generateTimeColumnAction() {
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($this->_generateTimeColumnData(date('Y-m-d')));
	}

	protected function _generateTimeColumnData($date) {
		$data = array();
		$timeStart = strtotime($date.' '.self::FILTER_TIME_START);
		$timeEnd = strtotime($date.' '.self::FILTER_TIME_END);
		$ctr = 0;
		while ($timeStart <= $timeEnd) {
			$tmp = array();
			$tmp['id'] = $timeStart;
			$tmp['data'][] = date('H:i',$timeStart);
			$data[] = $tmp;
			$timeStart = strtotime('+'.self::FILTER_MINUTES_INTERVAL.' minutes',$timeStart);
			$ctr++;
		}
		if ($ctr > 0) $data[0]['userdata']['numberOfRows'] = $ctr;
		$ret = array();
		$ret['rows'] = $data;
		return $ret;
	}

	public function newAppointmentAction() {
		$params = $this->_getParam('appointment');
		if (!is_array($params)) $params = array();
		$this->_editAppointment($params);
	}

	public function editAppointmentAction() {
		$params = array('appointmentId'=>(int)$this->_getParam('appointmentId'));
		$this->_editAppointment($params,'edit');
	}

	public function _editAppointment(Array $params,$action='new') {
		$appointmentId = isset($params['appointmentId'])?(int)$params['appointmentId']:0;
		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$date = isset($params['date'])?$params['date']:'';
		if ($appointmentId > 0 && $appointment->populate()) {
			$filter = $this->getCurrentDisplayFilter();
			$start = explode(' ',date('Y-m-d H:i', strtotime($appointment->start)));
			$date = $start[0];
			$appointment->start = $start[1];
			$appointment->end = date('H:i', strtotime($appointment->end));

			$recordNumber = $appointment->patient->record_number;
			$patientName = $appointment->patient->displayName;
			$this->view->patient = "{$patientName} #{$recordNumber} PID:{$appointment->patient->person_id}";
		}
		else {
			$start = isset($params['start'])?$params['start']:self::FILTER_TIME_START;
			$appointment->start = $start;
			$appointment->end = date('H:i',strtotime('+1 hour',strtotime($start)));
			$appointment->providerId = isset($params['providerId'])?(int)$params['providerId']:0;
			$appointment->roomId = isset($params['roomId'])?(int)$params['roomId']:0;
		}
		$this->view->date = $date;

		$form = new WebVista_Form(array('name'=>$action.'-appointment'));
		$form->setAction(Zend_Registry::get('baseUrl').'calendar.raw/process-'.$action.'-appointment');
		$form->loadORM($appointment,'Appointment');
		$form->setWindow('windowAppointmentId');
		$this->view->form = $form;

		$this->view->reasons = PatientNote::listReasons();

		$phones = array();
		$phone = new PhoneNumber();
		$phoneIterator = $phone->getIteratorByPersonId($appointment->patientId);
		foreach ($phoneIterator as $row) {
			$phones[] = $row->number;
		}
		$this->view->phones = $phones;

		$appointmentTemplate = new AppointmentTemplate();
		$this->view->appointmentReasons = $appointmentTemplate->getAppointmentReasons();

		$this->view->appointment = $appointment;
		$this->view->callbackId = $this->_getParam('callbackId','');
		$this->render('appointment');
	}

	public function processNewAppointmentAction() {
		$params = $this->_getParam('appointment');
		$this->_processEditAppointment($params);
	}

	public function processEditAppointmentAction() {
		$params = $this->_getParam('appointment');
		$this->_processEditAppointment($params);
	}

	public function _processEditAppointment(Array $params) {
		$paramProviders = array();
		foreach ($params as $key=>$val) {
			$providerPrefix = 'providerId-';
			if (substr($key,0,strlen($providerPrefix)) == $providerPrefix) {
				$paramProviders[] = $val;
				unset($params[$key]);
			}
		}
		if (count($paramProviders) > 0) {
			// assign the first providerId
			$params['providerId'] = array_shift($paramProviders);
		}
		// extra providers if any, can be retrieved using $paramProviders variable, not sure where to place it
		$forced = (int)$this->_getParam('forced');
		$filter = $this->getCurrentDisplayFilter();

		$app = new Appointment();
		if (isset($params['appointmentId']) && $params['appointmentId'] > 0) {
			$app->appointmentId = (int)$params['appointmentId'];
			$app->populate();
		}
		$app->populateWithArray($params);
		if ($app->appointmentId > 0) {
			$app->lastChangeId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$app->lastChangeDate = date('Y-m-d H:i:s');
		}
		else {
			$app->creatorId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$app->createdDate = date('Y-m-d H:i:s');
		}
		$date = isset($params['date'])?date('Y-m-d',strtotime($params['date'])):date('Y-m-d');
		$app->walkin = isset($params['walkin'])?1:0;
		$app->start = $date.' '.date('H:i:s', strtotime($params['start']));
		$app->end = $date.' '.date('H:i:s', strtotime($params['end']));

		$data = array();
		if (!$forced && $error = $app->checkRules()) { // prompt the user if the appointment being made would be a double book or is outside of schedule time.
			$data['error'] = $error;
		}
		else {
			$app->persist();
			//trigger_error(print_r($params,true));
			$data['appointmentId'] = $app->appointmentId;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function changeTimeAppointmentAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$time = date('H:i:s',strtotime($this->_getParam('time')));
		$forced = (int)$this->_getParam('forced');
		$app = new Appointment();
		$app->appointmentId = $appointmentId;
		$data = array(
			'error'=>'Appointment does not exists',
		);
		if ($app->populate()) {
			$app->lastChangeId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$app->lastChangeDate = date('Y-m-d H:i:s');
			$startTime = strtotime($app->start);
			$endTime = strtotime($app->end);
			$diff = $endTime - $startTime;
			$x = explode(' ',date('Y-m-d H:i:s',$startTime));
			$date = $x[0];
			$start = strtotime($date.' '.$time);
			$end = $start + $diff;
			$app->start = date('Y-m-d H:i:s',$start);
			$app->end = date('Y-m-d H:i:s',$end);
			$data = array('appointmentId'=>$appointmentId);
			if (!$forced && $error = $app->checkRules()) { // prompt the user if the appointment being made would be a double book or is outside of schedule time.
				$data['confirmation'] = $error;
			}
			else {
				$app->persist();
				//trigger_error(print_r($params,true));
				$data['appointmentId'] = $app->appointmentId;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
