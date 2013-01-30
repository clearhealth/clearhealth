<?php
/*****************************************************************************
*       AppointmentController.php
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


class AppointmentController extends WebVista_Controller_Action {

	public function ajaxMarkAppointmentAction() {
		$appointmentId =(int) $this->_getParam('appointmentId');
		$mark = $this->_getParam('mark');
		$app = new Appointment();
		$app->appointmentId = $appointmentId;
		$app->populate();
		//todo: compare provided mark against eligible in matching enumeration
		$app->appointmentCode = $mark;
		$app->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);	
	}

	public function processCreateVisitAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$data = false;
		$ret = $this->_createVisit($appointmentId);
		if ($ret > 0) {
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _createVisit($appointmentId) {
		$ret = 0;
		if ($appointmentId > 0) {
			$appointment = new Appointment();
			$appointment->appointmentId = $appointmentId;
			$appointment->populate();
			$personId = (int)$appointment->patientId;
			$insuredRelationship = new InsuredRelationship();
			$insuredRelationship->personId = $personId;
			$visit = new Visit();
			$visit->patientId = $personId;
			$visit->activePayerId = $insuredRelationship->defaultActivePayer;
			$visit->roomId = $appointment->roomId;
			$visit->practiceId = $appointment->practiceId;
			$room = new Room();
			$room->roomId = $visit->roomId;
			$room->populate();
			$visit->buildingId = $room->buildingId;
			$visit->createdByUserId = $appointment->creatorId;
			$visit->lastChangeUserId = $appointment->lastChangeId;
			$visit->treatingPersonId = $appointment->providerId;
			$visit->encounterReason = $appointment->reason;
			$visit->dateOfTreatment = $appointment->start;
			$visit->timestamp = date('Y-m-d H:i:s');

			$visit->appointmentId = $appointment->appointmentId;
			$visit->persist();
			$visitId = (int)$visit->visitId;
			$payment = new Payment();
			foreach ($payment->getIteratorByAppointmentId($appointmentId) as $row) {
				$row->visitId = $visitId;
				$row->persist();
			}

			$miscCharge = new MiscCharge();
			foreach ($miscCharge->getIteratorByAppointmentId($appointmentId) as $row) {
				$row->visitId = $visitId;
				$row->persist();
			}

			$ret = $visitId;
		}
		return $ret;
	}

	public function addPaymentAction() {
		$columnId = -1;
		$appointmentId = (int)$this->_getParam('appointmentId');
		$visitId = (int)$this->_getParam('visitId');
		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$appointment->populate();
		$payment = new Payment();
		$payment->visitId = $visitId;
		$payment->personId = (int)$appointment->patientId;
		$payment->appointmentId = $appointmentId;
		if (!$visitId > 0) {
			$columnId = (int)$this->_getParam('columnId');
		}

		$form = new WebVista_Form(array('name'=>'paymentId'));
		$form->setAction(Zend_Registry::get('baseUrl').'appointment.raw/process-add-payment');
		$form->loadORM($payment,'Payment');
		$form->setWindow('winPaymentId');
		$this->view->form = $form;
		$this->view->visitId = $visitId;

		$guid = 'd1d9039a-a21b-4dfb-b6fa-ec5f41331682';
		$enumeration = new Enumeration();
		$enumeration->populateByGuid($guid);
		$closure = new EnumerationClosure();
		$this->view->paymentTypes = $closure->getAllDescendants($enumeration->enumerationId,1,true)->toArray('key','name');

		$this->view->columnId = $columnId;
		$this->render('add-payment');
	}

	public function processAddPaymentAction() {
		$params = $this->_getParam('payment');
		$payment = new Payment();
		$payment->populateWithArray($params);
		if (!$payment->visitId > 0) {
			$payment->visitId = $this->_createVisit($payment->appointmentId);
		}
		$payment->timestamp = date('Y-m-d H:i:s');
		$payment->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$payment->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function historyAction() {
		$this->view->personId = (int)$this->_getParam('personId');
		$this->render();
	}

	public function listHistoryAction() {
		$personId = (int)$this->_getParam('personId');
		$future = (int)$this->_getParam('future');
		$rows = array();

		$appointmentTemplate = new AppointmentTemplate();
		$reasons = $appointmentTemplate->getAppointmentReasons();
		$iterator = new AppointmentIterator(null,false);
		$filters = array(
			'patientId'=>$personId,
		);
		if ($future) $filters['start'] = date('Y-m-d');
		$iterator->setFilters($filters);
		foreach ($iterator as $app) {
			$personId = (int)$app->patientId;
			$appointmentId = (int)$app->appointmentId;
			$providerId = (int)$app->providerId;
			$roomId = (int)$app->roomId;

			list($dateStart,$timeStart) = explode(' ',$app->start);
			list($dateEnd,$timeEnd) = explode(' ',$app->end);

			$providerName = '';
			if ($providerId > 0) {
				$provider = new Provider();
				$provider->setPersonId($providerId);
				$provider->populate();
				$providerName = $provider->displayName;
			}
			$roomName = '';
			if ($roomId > 0) {
				$room = new Room();
				$room->setRoomId($roomId);
				$room->populate();
				$roomName = $room->displayName;
			}

			$routing = new Routing();
			$routing->personId = $personId;
			$routing->appointmentId = $appointmentId;
			$routing->providerId = $providerId;
			$routing->roomId = $roomId;
			$routing->populateByAppointments();
			$station = $routing->stationId;
			$reason = $app->reason;
			$appointmentReason = isset($reasons[$reason])?$reasons[$reason]['name']:'';
			$row = array();
			$row['id'] = $appointmentId;
			$row['data'] = array();
			$row['data'][] = $dateStart;
			$row['data'][] = $timeStart.' - '.$timeEnd;
			$row['data'][] = $providerName;
			$row['data'][] = $app->title;
			$row['data'][] = $roomName;
			$row['data'][] = $appointmentReason;
			$row['data'][] = $app->appointmentCode;
			$row['data'][] = $routing->stationId;
			$rows[] = $row;
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

}
