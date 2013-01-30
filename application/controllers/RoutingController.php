<?php
/*****************************************************************************
*       RoutingController.php
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
 * Controller Clearhealth Routing actions
 */
class RoutingController extends WebVista_Controller_Action {

	public function listStationPatientsAction() {
                $stationId = (int)$this->_getParam('stationId');
		$routing = new Routing();
                $routeIter = $routing->getIterator();
                $routeIter->setFilter(array('stationId' => $stationId));

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array());
        }

	public function listPatientStationsAction() {
                $personId = (int)$this->_getParam('personId');
		$routing = new Routing();
                $routeIter = $routing->getIterator();
                $routeIter->setFilter(array('personId' => $personId));
		$this->view->stations = $routeIter;
		header('Content-Type: application/xml;');
		$this->view->xmlHeader = '<?xml version="1.0" encoding="UTF-8"?>';
        }

	public function ajaxCurrentPatientStationAction() {
                $personId = (int) $this->_getParam('personId');
		$routing = new Routing();
                $routeIter = $routing->getIterator();
                $routeIter->setFilter(array('personId' => $personId));
		$routing = $routeIter->current();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array($routing->stationId));
        }


	public function ajaxSetCheckedInAction() {
		$routingId = (int)$this->_getParam('routingId');
		$routing = new Routing();
		$routing->routingId = (int)$routingId;
		$routing->populate();
		$routing->checkInTimestamp = date('Y-m-d H:i:s');
		$routing->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array(true));
	}


	function ajaxStationFromAppointmentAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$stationId = $this->_getParam('stationId');
		trigger_error('st: ' . $stationId,E_USER_NOTICE);
		$personId = '';
		$routing = new Routing();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$appointment->populate();

		//appointment already arrived, prevent a dupe
		if ($appointment->arrived > 0) {
               		return $json->direct(array(false));
		}
		$patientId = $appointment->patientId;
		$provider = new Provider();
		$provider->personId = $appointment->providerId;
		$provider->populate();
		if (strlen($stationId) > 0) {
			trigger_error('st: ' . $stationId,E_USER_NOTICE);
		}
		elseif (strlen($provider->routingStation)> 0) {
			$stationId = $provider->routingStation;
		}
		else if ($appointment->roomId >  0) {
			$room = new Room();
			$room->roomId = $appointment->roomId;
			$room->populate();
			if (strlen($room->routingStation) > 0) {
				$stationId = $room->routingStation;
			}
		}

		$routing->personId = $appointment->patientId;
		$routing->stationId = $stationId;
		$routing->appointmentId = $appointment->appointmentId;
		$routing->providerId = $appointment->providerId;
		$routing->roomId = $appointment->roomId;
		$routing->timestamp = date('Y-m-d H:i:s');
		if ($routing->personId > 0 && strlen($stationId) > 0) {
			$appointment->arrived = 1;
			$appointment->persist();
			$routing->persist();
                	return $json->direct(array(true));
		}
               	return $json->direct(array(false));
	}
	
	function ajaxSetNextStationAction() {
		$routingId = (int)$this->_getParam('routingId');
		$personId = (int)$this->_getParam('personId');
		$nextStationId = $this->_getParam('stationId');
		$routing = new Routing();
		$routing->routingId = $routingId;
		$routing->populate();

		//used when creating a new route entry but not from an appointment
		if ($personId > 0) $routing->personId = $personId;

		$routing->routingId = '';
		$routing->fromStationId = $routing->stationId;
		$routing->stationId = $nextStationId;
		$routing->timestamp = date('Y-m-d H:i:s',strtotime('now'));
		$routing->checkInTimestamp = '0000-00-00 00:00:00';
		$routing->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array(true));
	}

	function ajaxComplete($appointmentId,$nextStationId,$fromStationId) {
		$app = ORDataObject::factory('Appointment',(int)$appointmentId);
		if ($app->get('patient_id') > 0 ) {
		$this->ajaxNextStation((int)$appointmentId,(int)$nextStationId,(int)$fromStationId);
		$personId = $app->get('patient_id');
		$db = Celini::dbInstance();
		$sql = "INSERT into routing_archive select * from routing where person_id = " . (int)$personId;
		$db->execute($sql);
		$sql = "DELETE from routing where person_id = " . (int)$personId;	
		$db->execute($sql);
		}
	}

	function processNextStationByAppointmentAction() {
		$appointmentId = (int)$this->_getParam('appointmentId');
		$stationId = $this->_getParam('stationId');
		$appointment = new Appointment();
		$appointment->appointmentId = $appointmentId;
		$appointment->populate();

		$provider = new Provider();
		$provider->personId = $appointment->providerId;
		$provider->populate();

		$routing = new Routing();
		$routing->personId = $appointment->patientId;
		$routing->appointmentId = $appointment->appointmentId;
		$routing->providerId = $appointment->providerId;
		$routing->roomId = $appointment->roomId;
		$routing->populateByAppointments();
		$routing->fromStationId = $routing->stationId;
		$routing->stationId = $stationId;
		$routing->timestamp = date('Y-m-d H:i:s');
		$routing->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array(true));
	}

}
?>
