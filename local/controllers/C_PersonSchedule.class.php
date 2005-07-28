<?php
if (!defined('CALENDAR_ROOT')) {
	define("CALENDAR_ROOT",APP_ROOT. "/local/lib/Calendar/");
}
require_once CALENDAR_ROOT . 'Month/Weekdays.php';
require_once CALENDAR_ROOT . 'Month/Weeks.php';
require_once CALENDAR_ROOT . 'Day.php';
require_once CALENDAR_ROOT . 'Week.php';
require_once CALENDAR_ROOT . 'Decorator.php';

require_once APP_ROOT . "/local/controllers/C_Calendar.class.php";
require_once APP_ROOT . "/local/includes/CalendarController.class.php";

require_once APP_ROOT . "/local/ordo/Schedule.class.php";
require_once APP_ROOT . "/local/ordo/Practice.class.php";
require_once APP_ROOT . "/local/ordo/Building.class.php";
require_once APP_ROOT . "/local/ordo/Room.class.php";


class C_PersonSchedule extends CalendarController {

	var $template_mod;
	var $schedule;

	function C_PersonSchedule($template_mod = "general") {
		parent::CalendarController();
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", Cellini::link(true) . $_SERVER['QUERY_STRING']);
		$this->assign("TOP_ACTION", "don't use me");
		$this->assign("DAY_ACTION", Cellini::link('day_action'));
	}

	function default_action() {
		return $this->edit_action();
	}

	function list_action() {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		
		$c = new Schedule();
		$this->assign("schedules",$c->schedules_factory());
		$s = new Practice();
		$this->assign("practices",$s->practices_factory());
		$b = new Building();
		$this->assign("buildings",$b->buildings_factory());
		$r = new Room();
		$this->assign("rooms",$r->rooms_factory());
		
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_list.html");
	}
	
	function schedule_list_action() {
		
		$c = new Schedule();
		$this->assign("schedules",$c->schedules_factory());
		
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_schedule_list.html");
	}
		
	function edit_schedule_action($id = "",$date="") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		if (!is_object($this->schedule)) {
			$this->schedule = new Event($id);
		}
		
		$year = date('Y');
		$month = date('n');
		$day = date('d');
		
		if (!empty($date)) {
			$year = date('Y', strtotime($date));
			$month = date('n', strtotime($date));
			$day = date('d', strtotime($date));
		}

		// Create a week as usual
		$WeekGrid = new Calendar_Week($year,$month,$day);
		$ndate = date("Y-m-d",$WeekGrid->nextWeek("timestamp")); 
		$pdate = date("Y-m-d",$WeekGrid->prevWeek("timestamp"));
		
		// Pass it to the decorator and use the decorator from now on...
			
		$WeekGrid->build();
		$WeekGridArray = array();
		$WeekGridArray = $WeekGrid->fetchAll();
		$wda_keys = array_keys($WeekGridArray);
		$start_timestamp = $WeekGridArray[$wda_keys[0]]->gettimestamp();
		$end_timestamp = $WeekGridArray[$wda_keys[count($wda_keys)-1]]->gettimestamp();
		
		$events = Event::get_events("o.event_id = $id","week");
		//print_r($events);
		$this->assign_by_ref("days_events",$events);
		$this->assign("start_timestamp",$start_timestamp);
		$this->assign("end_timestamp",$end_timestamp);
		
		foreach ($WeekGridArray as $d) {
			$tdate = $d->thisYear() . "-" . $d->thisMonth() . "-" . $d->thisDay();
			$darr = $this->build_day_increments($tdate, 7,13);
			$incs[strtotime($tdate)] = $darr;
			
		}
		
		$this->assign("week_increments", $incs);
		//print_r($incs);
		
		//set to epoch so second counts represent time only, no days
		
		$header_increment = $this->build_day_increments("", 7,13, true);
		
		$this->assign("header_increment", $header_increment);	
		
		//assigns tablemap and colmap which are matrixs used to figure out how many event in a given increment and what the colspans should be respectively
		$map = $this->build_table_map_week($incs,$events);
		$this->assign("week_tablemap",$map['tablemap']);
		$this->assign("week_tablemap2",$map['tablemap2']);
		$this->assign("week_maxcols",$map['maxcols']);
		
		$this->assign_by_ref("WeekGridArray",$WeekGridArray);
		
		$this->assign("WeekGrid",$WeekGrid);
		
		$this->assign("schedule",$this->schedule);
		$p = new Practice();
		$pa = $p->practices_factory();
		$r = new Room();
		$this->assign("rooms_practice_array",$r->rooms_practice_factory($pa[0]->get_id()));
		
		$this->assign("practices",$this->utility_array($pa,"id","name"));
		
		$s = new Schedule($this->schedule->get_foreign_id());
		$this->assign("schedule_user_id",$s->get_user_id());
		
		if (!isset($this->_tpl_vars['edit_event']))	$this->assign("edit_event",new Event());
		if (!isset($this->_tpl_vars['edit_timeplace']))	$this->assign("edit_timeplace",new Occurence());
		$this->assign("process",true);
		$this->assign("EVENT_ACTION", $this->_link("edit_event",true) . "id=$id");
		$this->assign("DELETE_ACTION", $this->_link("delete",true));
		$this->assign("OCCURENCE_ACTION", $this->_link("edit_occurence",true) . "id=$id&date=$date");
		$this->assign("WEEK_NEXT_ACTION", $this->_link("edit_schedule",true) . "id=$id&date=$ndate");
		$this->assign("WEEK_PREV_ACTION", $this->_link("edit_schedule",true) . "id=$id&date=$pdate");
		
		$this->assign("LINK_BASE",$this->_link('edit_schedule',true));
		$sidebar = $this->sidebar_action($month."/".$day."/".$year,$id);
		$this->assign_by_ref("sidebar",$sidebar);
		
		return $this->fetch($GLOBALS['template_dir'] . "person_schedules/" . $this->template_mod . "_edit_schedule.html");
	}
	
	function edit_schedule_action_process() {
		if ($_POST['process'] != "true")
			return;

		$id = 0;
		if (isset($_POST['id'])) $id = $_POST['id'];
		$errors = 0;
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		$this->schedule = new Event($id);
		$this->schedule->populate_array($_POST);
		$this->schedule->persist();
		$this->schedule->populate();
		$location_id = 0;
		if (isset($_POST['location_id']) && is_numeric($_POST['location_id'])) {
			$location_id = $_POST['location_id'];
		}
		
		$oc_template = array();
		if (isset($_POST['times']) && is_array($_POST['times'])) {
			$oc_template = $_POST['times'];
		}
		else {
			$this->messages->addMessage('', "You must select times for the schedule.");	
			$errors++;
		}
		
		if (empty($_POST['starting_date']) || empty($_POST['ending_date'])) {
			$this->messages->addMessage('', "You must specify a starting and ending date for the schedule."); 
			$errors++;	
		}
		
		
		$sdts = 0;
		$edts = 0;
		if (isset($_POST['starting_date'])) $sdts = strtotime($_POST['starting_date']);
		if (isset($_POST['ending_date']))$edts = strtotime($_POST['ending_date'] . " +1 day");
		
		if ($sdts > $edts) {
			$this->messages->addMessage("The dates provided for were invalid, its starting date must be before its ending date. The schedule could not be changed.");
			$errors++;	
		}
	
		foreach($oc_template as $day => $se) {
			if (count($se) % 2 != 0) {
				$this->messages->addMessage("You must have an even number of entries per day to represent In and Out times.");
				continue;
			}
		}
		
		if ($errors == 0) {
			
			$ocs = array();
			$d = new Calendar_Day(date("Y",$sdts),date("m",$sdts),date("d",$sdts));
			while ($d->getTimestamp() <= $edts) {
				$dts = $d->getTimestamp();
				$day_template = array();
				if (isset($oc_template[strtolower(date("l",$dts))]) && is_array($oc_template[strtolower(date("l",$dts))])) {
					$day_template = array_keys($oc_template[strtolower(date("l",$dts))]);
				}
				$oc = new Occurence();
				$oc->event_id = $this->schedule->get_id();
				$oc->user_id = $_POST['occurence_user_id'];
				$oc->set_location_id($location_id);
				for($i=0;$i<count($day_template);$i++) {
					if ($i%2 == 0) {
						$oc->start = date("Y-m-d H:i:s", ($day_template[$i]+$dts));
					}
					else {
						$oc->end = date("Y-m-d H:i:s", ($day_template[$i]+$dts));
						$ocs[] = $oc;
						$oc = new Occurence();
						$oc->event_id = $this->schedule->get_id();
						$oc->user_id = $_POST['occurence_user_id'];
						$oc->set_location_id($location_id);
					}	
				}
				$d = $d->nextDay("object");
			}
			
			foreach($ocs as $oc) {
				$oc->persist();	
			}
		}
		$_POST['process'] = "";
		$this->_state = false;
		return $this->edit_schedule_action($this->schedule->get_id());
	}
	
	function edit_event_action($id = "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		$this->assign("edit_event",new Event($id));
		
		return $this->edit_schedule_action($id);
	}
	
	function edit_timeplace_action($id = "", $fid= "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
		$this->assign("edit_timeplace",new Occurence($id));
		
		return $this->edit_schedule_action($fid);
	}
	
	function edit_event_action_process($schedule_id = "") {
		if ($_POST['process'] != "true")
			return;
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		$this->location = new Event($_POST['id']);
		$this->location->populate_array($_POST);
		
		$this->location->persist();
		
		$this->location->populate($this->location->get_id());
		$_POST['process'] = "";
		$this->_state = false;
		$this->location = null;
		header("Location: ".Cellini::link("edit_schedule").$_SERVER['QUERY_STRING']);
		//return $this->edit_schedule_action($schedule_id);
	}
	
	function edit_occurence_action_process($schedule_id = "") {
		if ($_POST['process'] != "true")
			return;
			$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
		$this->location = new Occurence($_POST['id']);
		$this->location->populate_array($_POST);
		$this->location->persist();
		
		$this->location->populate($this->location->get_id());
		$_POST['process'] = "";
		$this->_state = false;
		$this->location = null;
		header("Location: " . $this->_link("edit_schedule",true) . "id=" . $_POST['schedule_id']);
		return;
	}
	
	function delete_action($id = "",$object_class ="") {
		$this->sec_obj->acl_qcheck("delete",$this->_me,"",$object_class,$this,false);
		$message = "Incorrect parameters were passed, please check the query string and try again.";
		$allow_delete = false;
		preg_replace("/[^A-Za-z0-9]*/","",$object_class);
		$obj = null;
		if (!empty($id) && is_numeric($id)&& !empty($object_class)) {
			
			$obj = new $object_class($id);
			
			if (is_object($obj) && $obj->id > 0 && $obj->_populated && is_callable(array($obj,"get_delete_message"))) {
				$message = nl2br($obj->get_delete_message());
				$allow_delete = true;
			}
			else{
				$message = "No object with that information could be found or it does not support deletion";
			}	
		
		}
		$this->assign("message",$message);
		$this->assign("allow_delete",$allow_delete);
		//$this->assign("DELETE_ACTION", $this->_link("delete",true) . "id=$id&object_class=$object_class");
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_delete.html");
	}
	
	function delete_action_process($id = "",$object_class ="") {
		
		$this->sec_obj->acl_qcheck("delete",$this->_me,"",$object_class,$this,false);
		
		if ($_POST['process'] == true && (isset($_POST['cancel']) || isset($_GET['cancel']))) {
			$location = Cellini::link('list','location');
			$trail = $_SESSION['trail'];
			foreach($trail as $stop) {
				if (!isset($stop['delete']) && $stop['action'] != "delete") {
					if (isset($stop['main'])) array_shift($stop);
					$aks = array_keys($stop);
					$location = Cellini::link($stop[$aks[1]],$stop[$aks[0]]);
					unset($stop[$aks[0]]);
					unset($stop[$aks[1]]);
					foreach ($stop as $qn => $qi) {
					$location .= "$qn";
					if (!empty($qi)) $location .= "=$qi";
						$location .="&";
					}
					break;
				}
			}
		
			
			echo $location . "<br>";
			exit;
			header("Location: $location");
			return;
		}
		elseif ($_POST['process'] != "true" && (isset($_POST['delete']) || $_GET['delete'])) {
			return;
		}
		$error = true;
		if(empty($id) && empty($object_class)) {
			$id = $_POST['id'];
			$object_class = $_POST['object_class'];	
		}
		
		preg_replace("/[^A-Za-z0-9]*/","",$object_class);
		$obj = null;
		
		if (is_numeric($id) && !empty($object_class)) {
			
			$obj = new $object_class($id);
			if (is_object($obj) && $obj->id > 0 && $obj->_populated && is_callable(array($obj,"delete"))) {
				if ($obj->delete()) {
					$message = "Object(s) deleted successfully";
					$error = false;
				}
				else {
					$message = "Object deletion failed";	
				}
			}
			else{
				$message = "No object with that information could be found or it does not support deletion";
			}	
			
		}
		$this->assign("message", $message);
		if ($error) {
			$this->assign("error",true);
			$this->_state = false;
			return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_delete.html");	
		}
		$location = Cellini::link('list','location');
		$trail = $_SESSION['trail'];
		foreach($trail as $stop) {
			if (!isset($stop['delete']) && $stop['action'] != "delete") {
				if (isset($stop['main'])) array_shift($stop);
				$aks = array_keys($stop);
				$location = Cellini::link($stop[$aks[1]],$stop[$aks[0]]);
				unset($stop[$aks[0]]);
				unset($stop[$aks[1]]);
				foreach ($stop as $qn => $qi) {
					$location .= "$qn";
					if (!empty($qi)) $location .= "=$qi";
					$location .="&";
				}
				break;
			}
		}

		header("Location: $location");
		return;
	}
	function sidebar_action($date = "",$id="") {
		$this->sec_obj->acl_qcheck("usage",$this->_me,"","calendar",$this,false);
		if (empty($date)){
			$year = date('Y');
			$month = date('n');
			$day = date('d');
		}
		else {
			$year = date('Y', strtotime($date));
			$month = date('n', strtotime($date));
			$day = date('d', strtotime($date));
		}
		
		$week_select = array();
		$month_select = array();
		
		$tw = new Calendar_Week($year,$month,$day,0);
		$tw->build();
		$twa = $tw->fetchall();
		$first = array_shift($twa);
		$this->assign("week_selected",$first->year . "-" . $first->month . "-" .$first->day);
		
		$newdate = strtotime($year . "-" . $month . "-". $day . " -12 weeks");
		
		$tw = new Calendar_Week(date("Y",$newdate),date("m",$newdate),date("d",$newdate));
		$tw->build();
		for($i=0;$i<24;$i++) {
			$twa = $tw->fetchAll();
			$first = array_shift($twa);
			$last = array_pop($twa);
			$sts = $first->gettimestamp();
			$ets = $last->gettimestamp();
			$week_select[date("Y-n-j",$first->gettimestamp())] =  date("M. d",$sts) . " - " . date("M. d", $ets);
			$tw = $tw->nextWeek("object");
			$tw->build();
		}
		$this->assign("week_select",$week_select);
		
		$tm = new Calendar_Month($year,$month,$day);
		$tm->build();
		$tma = $tm->fetchall();
		$first = array_shift($tma);
		$this->assign("month_selected",$first->year . "-" . $first->month . "-" .$first->day);
		
		$newdate = strtotime($year . "-" . $month . "-". $day. " -12 months");
		
		$tm = new Calendar_Month(date("Y",$newdate),date("m",$newdate),date("d",$newdate));
		$tm->build();
		for($i=0;$i<24;$i++) {
			$tma = $tm->fetchAll();
			$first = array_shift($tma);
			$sts = $first->gettimestamp();
			$month_select[date("Y-n-j",$first->gettimestamp())] =  date("F, Y",$sts);
			$tm = $tm->nextMonth("object");
			$tm->build();
		}
		$this->assign("month_select",$month_select);
		
		$tmonth = new Calendar_Month_WeekDays($year,$month,0);
		$tmonth->build(array(new Calendar_Day(date("Y"),date("m"),date("d"))));
		//$pmonth = new Calendar_Month_WeekDays(date("Y",$tmonth->prevMonth("timestamp")),$tmonth->prevMonth("int"));
		//$pmonth->build();
		$nmonth = new Calendar_Month_WeekDays(date("Y",$tmonth->nextMonth("timestamp")),$tmonth->nextMonth("int"),0);
		$nmonth->build();
		$months = array($tmonth, $nmonth);
		
		$p = new Practice();
		$pa = $p->practices_factory();
		$r = new Room();
		
		//false is because we do not want a blank inserted at the beginning of the array
		$this->assign("rooms_practice_array",$r->rooms_practice_factory($pa[0]->get_id(),false));
		
		$u = new User(null,null);
		$this->assign("users_array",$this->utility_array($u->users_factory(),"id","username"));
		if (isset($_SESSION['calendar']['filters']['user'])) {
			$this->assign("selected_user",$_SESSION['calendar']['filters']['user']);
		}
		if (isset($_SESSION['calendar']['filters']['location'])) {
			$this->assign("selected_location",$_SESSION['calendar']['filters']['location']);
		}
		
		$this->assign_by_ref("sidebar_months",$months);
		$this->assign("LINK_BASE",$this->_link('edit_schedule',true) . "id=" . $id . "&");
		 
		return $this->fetch($GLOBALS['template_dir'] . "person_schedules/" . $this->template_mod . "_sidebar.html");
	}
	
}

?>
