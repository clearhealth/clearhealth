<?php
/**
 * Default Event Comparer
 */
class EventComparator_General {
	var $controller;

	function checkDoubleBook($oc,$event) {
		$start = $oc->get_start();
		$end = $oc->get_end();
		$provider_id = $oc->get_user_id();
		$location_id = $oc->get_location_id();
		
		$db = Celini::dbInstance();
		
		if (empty($start) || empty($end) || empty($provider_id)){
			echo "Date or provider information is invalid.";
		}
		else {
			$sdate = date('Y-m-d H:i:00', strtotime($start));
			$edate = date('Y-m-d H:i:00', strtotime($end));
		}
		
		$sql = 	"SELECT o.id FROM occurences as o LEFT JOIN `events` as e on e.id=o.event_id LEFT JOIN schedules as s on s.id=e.foreign_id ".
				"WHERE ((((s.schedule_code != 'PS' AND s.schedule_code != 'NS') OR s.schedule_code IS NULL) and o.user_id =" . $db->qstr($provider_id) . ") OR s.schedule_code = 'ADM') AND " .
				"(('$sdate' <= `start` AND '$edate' >= `end`) OR ".
				"('$edate' > `start` AND '$edate' <= `end`) OR ".
				"('$sdate' >= `start` AND '$sdate' < `end`)) "; 
		
		if (is_numeric($location_id)) {
			$sql .= " AND o.location_id =" . $db->qstr($location_id);	
		}
		
		$results = $db->query($sql);
		
		while ($results && !$results->EOF) {
			// Retrieve mock event array and create display
			$this->controller->assign("ev", $this->_createMockEventArray($oc));
			$app_display = $this->controller->view->render("appointment_inline_blurb.html");
			
			
			// Now setup real data
			$o = new Occurence($results->fields['id']);
			$e = new Event();
			$ea = $e->get_events("o.id = " . $o->get_id());
			$eak = array_keys($ea);
			
			//the get events function returns events in an array broken out by timestamp, we just want the details on the specific event 
			if (isset($eak[0]) && !empty($ea[$eak[0]][0])) {
				$e = $ea[$eak[0]][0];
			}
			else {
				$this->assign("double_book_message", "An event was found that conflicted but its information could not be found, this may be the result of a corrupted event, id: '" . $o->get_id() . "'");
			}
			
			$this->controller->assign("ev",$e);
			$capp_display = $this->controller->view->render("appointment_inline_blurb.html");
			$emsg = "";
			if ($this->controller->isAssigned('double_book_message')) {
				$emsg = $this->controller->get_template_vars('double_book_message') . "<br />";
			}
			$this->controller->assign("double_book_message", $emsg."You supplied this information:" . $app_display ."<br />But that collides with another event: <br>" . $capp_display);
			$results->moveNext();
			if ($results->EOF) {
				return true;		
			}
			
		}
		
		return false;
	}

	/**
	 * Creates a mock event array based on an occurence
	 *
	 * @param  Occurence
	 * @return array
	 * @access private
	 *
	 * @todo Consider refractoring this into Occurence so it can create its
	 *   own array based on itself.
	 */
	function _createMockEventArray(&$oc) {
		// Sanity check until type hinting in PHP 5
		assert('is_a($oc, "Occurence")');
		
		$returnArray = array();
		//populate event array format from oc object
		$returnArray['start_ts']    = $oc->get('start_timestamp');
		$returnArray['end_ts']      = $oc->get('end_timestamp');
		$returnArray['reason_code'] = $oc->get('reason_code');
		
		// Pull user information in
		$u = $oc->get_user();
		$returnArray['nickname'] = $u->get('nickname');
		$returnArray['color']    = $u->get('color');
		
		// Finally, create patient information
		$p =& ORDataObject::factory('Patient', $oc->get('external_id'));
		$returnArray['notes']            = $oc->get_notes();
		$returnArray['p_lastname']       = $p->get('last_name');
		$returnArray['p_firstname']      = $p->get('first_name');
		$returnArray['dob']              = $p->get('date_of_birth');
		$returnArray['p_record_number']  = $p->get('record_number');
		$returnArray['p_patient_number'] = $p->get('patient_number');
		$returnArray['p_phone']          = $p->get('phone');
		$returnArray['age']              = $p->get('age');
		
		return $returnArray;
	}
}
