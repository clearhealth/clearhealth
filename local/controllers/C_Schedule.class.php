<?php
define("CALENDAR_ROOT",APP_ROOT. "/local/lib/Calendar/");
require_once CALENDAR_ROOT . 'Month/Weekdays.php';
require_once CALENDAR_ROOT . 'Month/Weeks.php';
require_once CALENDAR_ROOT . 'Day.php';
require_once CALENDAR_ROOT . 'Week.php';
require_once CALENDAR_ROOT . 'Decorator.php';

require_once APP_ROOT . "/local/includes/CalendarController.class.php";
require_once APP_ROOT . "/local/controllers/C_Location.class.php";

require_once APP_ROOT . "/local/ordo/Schedule.class.php";
require_once APP_ROOT . "/local/ordo/Practice.class.php";
require_once APP_ROOT . "/local/ordo/Building.class.php";
require_once APP_ROOT . "/local/ordo/Room.class.php";
require_once APP_ROOT . "/local/ordo/Patient.class.php";

class C_Schedule extends CalendarController {

	var $template_mod;

	function C_Schedule($template_mod = "general") {
		parent::CalendarController();
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", Celini::link(true) . $_SERVER['QUERY_STRING']);
		$this->view->path = 'calendar';
	}

	function default_action() {
		return "";
	}
	
	//args coming in is usually _POST
	function confirm_action($args) {
		
		if (empty($args)){
			$this->messages->addMessage("Date or provider information is not correct, please check the query string and try again.");
		}

		$this->assign("user_id",$args['user_id']);
		$this->assign("location_id",$args['location_id']);
		$this->assign("occurence_id",$args['occurence_id']);
		$this->assign("date",$args['date']);
		if (isset($args['external_id'])) {
			$this->assign("external_id",$args['external_id']);
		}
		$this->assign("start_time",$args['start_time']);
		$this->assign("end_time",$args['end_time']);
		$this->assign("notes",$args['notes']);
		if (isset($args['reason_id'])) {
			$this->assign("reason_id", $args['reason_id']);
		}
		if (isset($args['users'])) {
			$this->assign("users", $args['users']);
		}
		$this->assign("FORM_ACTION", Celini::link('confirm','schedule',false));
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_confirm.html");
	}
	
	function confirm_action_process() {
		
		if ($_POST['process'] != "true")
			return;
		
		if (isset($_POST['cancel'])) {
			$location = Celini::link('list','location');
			$trail = $_SESSION['trail'];
			foreach($trail as $stop) {
					if (!isset($stop['edit_appointment']) && $stop['action'] != "edit_appointment" &&
						!isset($stop['confirm']) && $stop['action'] != "confirm") {
						if (isset($stop['main'])) array_shift($stop);
						$aks = array_keys($stop);
						$location = Celini::link($stop[$aks[1]],$stop[$aks[0]]);
						unset($stop[$aks[0]]);
						unset($stop[$aks[1]]);
						foreach ($stop as $qn => $qi) {
						//they were coming from editing this appointment which they are now cancelling, don't send this and put them back in to edit mode
						if ($qn === "appointment_id") continue;
						$location .= "$qn";
						if (!empty($qi)) $location .= "=$qi";
							$location .="&";
						}
						break;
					}
			}
			header("Location: " . $location);
			exit;
		}
	
		$string = "";
		$cl = new C_Location();
		$cl->edit_appointment_action_process($_POST['occurence_id'],true);
	
		$this->_state = false;
		return $string;
	}
	
	function check_availability($oc, $event) {
		$start = $oc->get('start');
		$end   = $oc->get('end');
		$provider_id = $oc->get('user_id');
		$location_id = $oc->get('location_id');
		$db = $GLOBALS['frame']['adodb']['db'];
		
		if (empty($start) || empty($end) || empty($provider_id)){
			//echo "Date or provider information is invalid.";
		}
		else {
			$startObj = TimestampObject::create($start);
			$sdate = $startObj->toString('%Y-%m-%d %H:%i:00');
			
			$endObj =& TimestampObject::create($end);
			$edate  = $endObj->toString('%Y-%m-%d %H:%i:00');
		}
		
		$sql = 	"SELECT e.id FROM occurences as o LEFT JOIN `events` as e on e.id=o.event_id LEFT JOIN schedules as s on s.id=e.foreign_id ".
				"WHERE s.schedule_code = 'PS' and o.user_id =" . $db->qstr($provider_id) . " AND " .
				"('$sdate' >= `start` AND '$edate' <= `end`)";
		
		if (is_numeric($location_id)) {
			$sql .= " AND o.location_id =" . $db->qstr($location_id);	
		}
				
		$results = $db->query($sql);
		
		if ($results && $results->RecordCount() > 0) {
			return true;	
		}
		
		$sql = "SELECT concat_ws(' ',p.first_name, p.last_name) as resource_name from user u  inner join person p on p.person_id = u.person_id "
				." where u.user_id =" . $db->qstr($provider_id);
		$results = $db->query($sql);
		if ($results && !$results->EOF) {
			$this->assign("availability_message", "It does not appear that " . $results->fields['resource_name'] . " is available during $sdate - $edate for that facility.");
		}
		else {
			$this->assign("availability_message", "It does not appear that that resource is available during $sdate - $edate for that facility.");
		}
		return false;
	}
	
	
	function check_double_book($oc,$event,$sum) {
		$GLOBALS['loader']->requireOnce('includes/EventComparator_General.class.php');
		$calc = new EventComparator_General();
		$calc->controller =& $this;
		return $calc->checkDoubleBook($oc,$event,$sum);
	}
	
	function set_filter_action($filter) {
	
		$filters = $_SESSION['calendar']['filters'];
		
		$segments = split("\|",$filter);
		foreach($segments as $segment) {
			$ts = split("/",$segment);
			$subseg[$ts[0]] = $ts[1];
			foreach ($subseg as $type => $seg) {
				$filters[$type] = $seg;	
			}
		}
		
		$_SESSION['calendar']['filters'] = $filters;
				
		$trail = array_reverse($_SESSION['trail']);
		$location = "";

		foreach($trail as $stop) {
			
			if (!isset($stop['set_filter']) && $stop['action'] != "set_filter") {
				foreach ($trail[1] as $qn => $qi) {
					$location .= "$qn";
					if (!empty($qi)) $location .= "=$qi";
					$location .="&";
				}				
				break;
			}
		}

		header("Location: controller.php?" . $location);
		
		$this->_state = false;
		return;
	}
		
}

?>
