<?php

$loader->requireOnce('datasources/RoutingStation_DS.class.php');
$loader->requireOnce('includes/Grid.class.php');

/**
 * Controller Clearhealth Routing actions
 */
class C_Routing extends Controller {

	function actionList() {
		$prof = Celini::getCurrentUserProfile();
		$currentPractice = $prof->getCurrentPracticeId();
		$em =& Celini::enumManagerInstance();
		$routeEnumList = $em->enumList('routing_stations');
                $stations = $routeEnumList->toArray(false);
		$filteredStations = array();
                for($i=0;$i<count($stations);$i++) {
			if ($stations[$i]['status'] == 0) continue;
                        $id = $stations[$i]['key'];
                        $name = $stations[$i]['value'];
			
			$stationExtra = split(',',$stations[$i]['extra2']);
                        if (in_array($currentPractice,$stationExtra) || strlen($stations[$i]['extra2']) == 0) {
                                if ($id == 0) continue;
				$filteredStations[$stations[$i]['key']] = $stations[$i];
                        }
                }
		$this->view->assign('stations',$filteredStations);
		return $this->view->render("list.html");
	}

	function actionShowStation($stationSysName) {
		$rsDS = "";
		$stationName = "";
		$stationId = 0;
		$em =& Celini::enumManagerInstance();
		if (is_numeric($stationSysName)) {
		$rsDS = new RoutingStation_DS((int)$stationSysName);
		$stationName = $em->lookup('routing_stations',(int)$stationSysName);
		$stationId = (int)$stationSysName;
		}
		else {
		$stationsBySysName = $em->enumArray('routing_stations','extra1','key');
		$rsDS = new RoutingStation_DS($stationsBySysName[$stationSysName]);
		$stationName = $em->lookup('routing_stations',$stationsBySysName[$stationSysName]);
		$stationId = $stationsBySysName[$stationSysName];
		}
		$grid =& new cGrid($rsDS);

		$this->view->assign('stationName',$stationName);
		$this->view->assign('stationId',$stationId);

                $this->view->assign_by_ref('grid',$grid);
		return $this->view->render('stationView.html');

	}

	function ajaxShowStationGrid($stationId) {
                $rsDS = new RoutingStation_DS((int)$stationId);
                $grid =& new cGrid($rsDS);
		$gridOutput = $grid->render();
		if ($grid->empty) {
			return 'There are no arrivals. <a href="javascript:void(0);" onClick="' . "HTML_AJAX.replace('stationGrid','crouting','ajaxShowStationGrid'," . (int)$stationId . ');">Click here to refresh</a>.';
		}
		return $gridOutput;
	}

	function actionShowStationXML($stationId,$practiceId = 0) {
                $rsDS = new RoutingStation_DS((int)$stationId,(int)$practiceId);
		$rsDS->_type = "raw";
		$rsDS->prepare();
		$stationArray = $rsDS->toArray();
		return ORDataObject::toXml($stationArray);
	}

	function actionStationStatusXML($stationId) {
		$sql = "select max(timestamp) as lastchange, count(timestamp) as patients
			from routing rt where rt.station_id = " . (int)$stationId . " group by rt.station_id";
		$db = Celini::dbInstance();
		$result = $db->getAll($sql);
                return ORDataObject::toXml($result);
        }

	function actionShowStationRecentGrid() {

	}

	function actionStationFromAppointment($appointmentId) {
		return $this->ajaxStationFromAppointment($appointmentId);
	}

	function ajaxStationFromAppointment($appointmentId) {
		$stationId = 0;
		$patientId = 0;
		$routing = ORDataObject::factory("Routing");
		$appointment = ORDataObject::factory("Appointment",(int)$appointmentId);
		//this appointment is already arrived, prevent a duplicate arrival
		/*if ($appointment->get('arrived') > 0) {
			return;
		}*/
		$patientId = $appointment->get('patient_id');
		$provider = ORDataObject::factory('Provider');
		$providerStations = $provider->valueList('routing_station');
		//appointment provider routing station is a higher priority over appointment room routing station
		if ($appointment->get('provider_id') >  0 && $providerStations[$appointment->get('provider_id')] > 0) {
			$stationId = $providerStations[$appointment->get('provider_id')];
		}
		else if ($appointment->get('room_id') >  0) {
			$room = ORDataObject::factory('Room');
			$roomStations = $room->valueList('routing_station');
			if ($roomStations[$appointment->get('room_id')] > 0) {
				$stationId = $roomStations[$appointment->get('room_id')];
			}
		}

		$routing->set('person_id',(int)$patientId);
		$routing->set('station_id',(int)$stationId);
		$routing->set('appointment_id',$appointment->get('appointment_id'));
		$routing->set('provider_id',$appointment->get('provider_id'));
		$routing->set('room_id',$appointment->get('room_id'));
		$routing->set('timestamp',date('Y-m-d H:i:s',strtotime('now')));
		if ($patientId > 0 && $stationId > 0) {
			$appointment->set('arrived',1);
			$appointment->persist();
			$routing->persist();
			return true;
		}
		return false;
	}
	
	function ajaxNextStation($appointmentId,$nextStationId,$fromStationId) {
		$routing = ORDataObject::factory("Routing");
		$appointment = ORDataObject::factory("Appointment",(int)$appointmentId);
		$patientId = $appointment->get('patient_id');

		//appointment provider routing station is a higher priority over appointment room routing station
		$routing->set('person_id',(int)$appointment->get('patient_id'));
		$routing->set('station_id',(int)$nextStationId);
		$routing->set('from_station_id',(int)$fromStationId);
		$routing->set('appointment_id',(int)$appointmentId);
		$routing->set('provider_id',(int)$appointment->get('provider_id'));
		$routing->set('room_id',(int)$appointment->get('room_id'));
		$routing->set('timestamp',date('Y-m-d H:i:s',strtotime('now')));
		$routing->persist();
		return true;
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
	
}
?>
