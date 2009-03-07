<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class RoutingStation_DS extends Datasource_sql 
{
	var $_internalName = 'RoutingStation_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	var $_stationId = 0;
	var $currentPractice = 0;


	function RoutingStation_DS($stationId,$practiceId = 0) {
		$stationId = (int)$stationId;
		$this->_stationId = $stationId;
		$em =& Celini::enumManagerInstance();
		$enumId = (int)$em->enumList('routing_stations')->id;
		$prof = Celini::getCurrentUserProfile();
                $this->currentPractice = $prof->getCurrentPracticeId();
		if ((int)$practiceId > 0) {
			$this->currentPractice = (int)$practiceId;
		}
		$noteReason = Celini::config_get('arrival:patientNoteReason');
		$noteJoin = '';
		$pnCol = '';
		if (strlen($noteReason) > 0 && $em->lookupKey('patient_note_reason',$noteReason) > 0) {
			$reasonKey = $em->lookupKey('patient_note_reason',$noteReason);
			$noteJoin = " left join patient_note pn on pn.patient_id = p.person_id and pn.reason = " . $reasonKey . " and pn.note_date >= DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') and pn.note_date <= DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59') and pn.deprecated = 0";
			$pnCol = "group_concat(pn.note separator '<br />') as note, ";
		}
		$labels = array(
                        'patient'     => 'Patient',
			'provider' => 'Provider',
			'building' => 'Building',
			'room' => 'Room',
                        'record_number' => 'MRN',
                //        'station_id'    => 'Arriving',
                        'from_station_id' => 'Coming From',
                        'note'     => 'Note',
                        'minutes'     => 'Waiting Minutes',
                        'routeto'     => 'Route To'
                );
	
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "concat(p.last_name,', ' , p.first_name, ' ' , substring(p.middle_name,0,1)) as patient,
					concat(prov.first_name, ', ', prov.last_name) as provider,
					b.name as building,
					rm.name as room,
					pat.record_number,
					rt.station_id,
					rt.from_station_id,
					{$pnCol}
					TIMESTAMPDIFF(MINUTE ,rt.timestamp, NOW()) AS 'minutes',
					p.person_id,
					rt.appointment_id",
				'from'    => "routing rt 
					inner join person p on p.person_id = rt.person_id
					inner join patient pat on pat.person_id = rt.person_id
					inner join enumeration_value ev on ev.enumeration_id = {$enumId}  and ev.key = rt.station_id and 
					(
					replace(substring(substring_index(ev.extra2, ',', 1), length(substring_index(ev.extra2, ',', 1 - 1)) + 1), ',', '')  = '{$this->currentPractice}' 
					or replace(substring(substring_index(ev.extra2, ',', 2), length(substring_index(ev.extra2, ',', 2 - 1)) + 1), ',', '')  = '{$this->currentPractice}' 
					or ev.extra2 = ''
					)
					inner join (select max(routing_id) as routing_id from routing group by person_id) as ids on rt.routing_id = ids.routing_id
					left join person prov on prov.person_id = rt.provider_id 
					left join rooms rm on rm.id = rt.room_id 
					left join buildings b on b.id = rm.building_id 
 " . $noteJoin,
				'where'   => "rt.station_id = {$stationId}
					and rt.timestamp >= DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') 
					and rt.timestamp <= DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')",
				//'orderby' => 'timestamp DESC',
				'groupby' => 'p.person_id'
			),
			$labels);
			$this->registerFilter('station_id', array(&$this, '_stationLookup'),false,"html");
			$this->registerFilter('from_station_id', array(&$this, '_stationLookup'),false,"html");
			$this->registerFilter('patient', array(&$this, '_patientLink'),false,"html");
			$this->registerFilter('routeto', array(&$this, '_routeto'),false,"html");
		//echo $this->preview();
        }

        function _stationLookup($value) {
                $em =& Celini::enumManagerInstance();
                return $em->lookup('routing_stations', $value);
        }
	function _patientLink($value, $row) {
		return '<a href="' . Celini::link('view','PatientDashboard',true,$row['person_id']) .'">' . $value . '</a>';
	}
	function _routeto($value, $row) {
                $em =& Celini::enumManagerInstance();
		$routeStr = "<select onChange=\"handleStationChange(" .  $row['appointment_id'] . ",this.options[this.selectedIndex].value,'Move patient to station: ' + this.options[this.selectedIndex].innerHTML)\">";
		$routeStr .= '<option value="noselection">Select Station</option>';
		$routeEnumList = $em->enumList('routing_stations');
		$stations = $routeEnumList->toArray(false);
		for($i=0;$i<count($stations);$i++) {
			$id = $stations[$i]['key'];
			$name = $stations[$i]['value'];
			//skip over current station
			if ($stations[$i]['key'] == $this->_stationId || $stations[$i]['status'] == 0) continue;
			$stationsExtra = split(',',$stations[$i]['extra2']);
			if (in_array($this->currentPractice,$stationsExtra) || empty($stations[$i]['extra2'])) {
			
				//loop over first which is usually 'No Station'
				if ($id == 0) continue;
				$routeStr .= '<option value="' . $id . '">' . $name . '</option>';
			}
		}
		//add routing action link for completed
		$routeStr .= '<option value="complete">Complete</option>';
		$routeStr .= '</select>';
		return $routeStr;
	}

}

