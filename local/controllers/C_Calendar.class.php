<?php
if (!defined("CALENDAR_ROOT")) {
	define("CALENDAR_ROOT",APP_ROOT. "/local/lib/Calendar/");
}
require_once CALENDAR_ROOT . 'Month/Weekdays.php';
require_once CALENDAR_ROOT . 'Month/Weeks.php';
require_once CALENDAR_ROOT . 'Day.php';
require_once CALENDAR_ROOT . 'Week.php';
require_once CALENDAR_ROOT . 'Decorator.php';
require_once APP_ROOT . "/local/includes/CalendarController.class.php";
require_once APP_ROOT . "/local/ordo/Practice.class.php";
require_once APP_ROOT . "/local/ordo/Building.class.php";
require_once APP_ROOT . "/local/ordo/Room.class.php";
require_once APP_ROOT . "/local/ordo/Schedule.class.php";
require_once APP_ROOT . "/local/includes/calendarDecorators/MonthDecorator.class.php";
require_once APP_ROOT . "/local/includes/calendarDecorators/WeekDecorator.class.php";
require_once APP_ROOT . "/local/includes/calendarDecorators/WeekGridDecorator.class.php";
require_once APP_ROOT . "/local/includes/calendarDecorators/DayDecorator.class.php";
require_once APP_ROOT . "/local/includes/calendarDecorators/DayBriefDecorator.class.php";

class C_Calendar extends CalendarController {

	var $template_mod;
	var $location;

	function C_Calendar($template_mod = "general") {
		parent::CalendarController();
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", Cellini::link(true) . $_SERVER['QUERY_STRING']);
		$this->assign("TOP_ACTION", Cellini::link(true));
		
		$current_link = Cellini::link(true);
		if (isset($_GET['date'])) $current_link .=  "date=" . $_GET['date'] . "&";
		$this->assign("APPOINTMENT_ACTION",$current_link);
		$this->assign('DAY_ACTION', Cellini::link('day'));
		$this->assign('PATIENT_EDIT_LINK', Cellini::link('edit','patient'));

		$this->assign("FILTER_ACTION",Cellini::managerLink('setFilter','today')."process=true&");
		$this->assign_by_ref("CONTROLLER", $this);
		$this->assign('DELETE_ACTION', Cellini::link('delete','Location'));
		
		$this->_setupFilterDisplay();
	}
		
	function _setupFilterDisplay() {
		//set display for location and users
		$users = "";
		$locations = "";

		if (!isset($_SESSION['calendar']['filters'])) {
			$_SESSION['calendar']['filters'] = null;
		}
		$filters = $_SESSION['calendar']['filters'];
		
		if (is_array($filters)) {
			
			foreach ($filters as $type => $filter) {
				
				if (!empty($filter)) {
					switch($type) {
						case 'user': 
							$u = new User(null,null);
							$u->set_id($filter);
							$u->populate();
							$users .= $u->get_username() . ", "; 
							break;
						case 'location':
							$r = new Room($filter);
							$b = $r->get_building();
							$locations .= $b->get_name() . "->" . $r->get_name() . ", ";
							break;	
					}
				}
			}
		}
		
		if (!empty($users)) {
			$users = substr($users,0,-2);
			$this->assign("users_filter", $users);
		}
		
		if (!empty($locations)) {
			$locations = substr($locations,0,-2);
			$this->assign("locations_filter", $locations);
		}
	}


	function default_action() {
		return $this->day_action();
	}

	function month_action($date = "") {
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

		// Create a month as usual
		$Month = new Calendar_Month_Weekdays($year,$month,0);
	
		$Month->build();
		$ndate = date("Y-m-d",$Month->nextMonth("timestamp")); 
		$pdate = date("Y-m-d",$Month->prevMonth("timestamp"));
		
		$MonthArray = array();
		$MonthArray = $Month->fetchAll();
		$mda_keys = array_keys($MonthArray);
		$start_timestamp = $MonthArray[$mda_keys[0]]->gettimestamp();
		$end_timestamp = $MonthArray[$mda_keys[count($mda_keys)-1]]->gettimestamp();
		
		$events = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp),"month","","ADM,PS");
		//print_r($events);
		$this->assign_by_ref("events",$events);
		$this->assign("start_timestamp",$start_timestamp);
		$this->assign("end_timestamp",$end_timestamp);		
		
		$this->assign_by_ref("Month",$Month);
		$this->assign_by_ref("MonthArray",$MonthArray);
		
		$this->assign("MONTH_NEXT_ACTION", $this->_link("month",true) . "date=$ndate");
		$this->assign("MONTH_PREV_ACTION", $this->_link("month",true) . "date=$pdate");
		
		$this->assign("Month",$Month);
		
		$sidebar = $this->sidebar_action($month."/".$day."/".$year, "month");
		$this->assign_by_ref("sidebar",$sidebar);
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_month.html");
	}
	
	function week_action($date = "") {
		
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

		// Creata a month as usual
		$Week = new Calendar_Week($year,$month,$day);
	
		// Pass it to the decorator and use the decorator from now on...
		
		$Week->build();
		$ndate = date("Y-m-d",$Week->nextWeek("timestamp")); 
		$pdate = date("Y-m-d",$Week->prevWeek("timestamp"));
		$WeekArray = array();
		$WeekArray = $Week->fetchAll();
		$wda_keys = array_keys($WeekArray);
		$start_timestamp = $WeekArray[$wda_keys[0]]->gettimestamp();
		$end_timestamp = $WeekArray[$wda_keys[count($wda_keys)-1]]->gettimestamp();
		
		$events = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp),"week");
		//print_r($events);
		$this->assign_by_ref("days_events",$events);
		$this->assign("start_timestamp",$start_timestamp);
		$this->assign("end_timestamp",$end_timestamp);		
		
		$this->assign_by_ref("Week",$Week);
		$this->assign_by_ref("WeekArray",$WeekArray);
		
		$this->assign("WEEK_NEXT_ACTION", $this->_link("week",true) . "date=$ndate");
		$this->assign("WEEK_PREV_ACTION", $this->_link("week",true) . "date=$pdate");
		
		$sidebar = $this->sidebar_action($month."/".$day."/".$year, "week");
		$this->assign_by_ref("sidebar",$sidebar);
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_week.html");
	}
		
	function week_grid_action($date = "") {
		
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

		// Create a week as usual
		$WeekGrid = new Calendar_Week($year,$month,$day);
		$ndate = date("Y-m-d",$WeekGrid->nextWeek("timestamp")); 
		$pdate = date("Y-m-d",$WeekGrid->prevWeek("timestamp"));
		
		$WeekGrid->build();
		$WeekGridArray = array();
		$WeekGridArray = $WeekGrid->fetchAll();
		$wda_keys = array_keys($WeekGridArray);
		$start_timestamp = $WeekGridArray[$wda_keys[0]]->gettimestamp();
		$end_timestamp = $WeekGridArray[$wda_keys[count($wda_keys)-1]]->gettimestamp();
		
		$events = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp+86400),"week","","PS",false);
		//print_r($events);
		$this->assign_by_ref("days_events",$events);
		
		$schedules = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp+86400),"week_schedule","","PS");
		//print_r($schedules);
		$this->assign_by_ref("schedule_events",$schedules);
		
		$this->assign("start_timestamp",$start_timestamp);
		$this->assign("end_timestamp",$end_timestamp);
		
		foreach ($WeekGridArray as $d) {
			$tdate = $d->thisYear() . "-" . $d->thisMonth() . "-" . $d->thisDay();
			$darr = $this->build_day_increments($tdate, 7,13);
			$incs[strtotime($tdate)] = $darr;
		}
		
		$this->assign("week_increments", $incs);
		
		//set to epoch so second counts represent time only, no days
		$header_increment = $this->build_day_increments("", 7,13, true);
		
		$this->assign("header_increment", $header_increment);	
		
		$map = $this->build_table_map_week($incs,$events);
		$this->assign("week_tablemap",$map['tablemap']);
		$this->assign("week_tablemap2",$map['tablemap2']);
		$this->assign("week_maxcols",$map['maxcols']);
		
		$map = $this->build_table_map_week($incs,$schedules);
		$this->assign("week_stablemap",$map['tablemap']);
		$this->assign("week_stablemap2",$map['tablemap2']);
		$this->assign("week_smaxcols",$map['maxcols']);
		
		$this->assign("WEEK_NEXT_ACTION", $this->_link("week_grid",true) . "date=$ndate");
		$this->assign("WEEK_PREV_ACTION", $this->_link("week_grid",true) . "date=$pdate");
		
		$this->assign_by_ref("WeekGrid",$WeekGrid);
		$this->assign_by_ref("WeekGridArray",$WeekGridArray);
		
		$sidebar = $this->sidebar_action($month."/".$day."/".$year, "week_grid");
		$this->assign_by_ref("sidebar",$sidebar);
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_week_grid.html");
	}

	function day_action($date="",$start="",$end="") {
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

		// Creata a day as usual
		$DayGrid = new Calendar_Day($year,$month,$day);
		$ndate = date("Y-m-d",$DayGrid->nextDay("timestamp")); 
		$pdate = date("Y-m-d",$DayGrid->prevDay("timestamp"));
		
		$incs = $this->build_day_increments($year . "-" . $month . "-" . $day, 7,13);
		$this->assign("increments", $incs);
	
		// Pass it to the decorator and use the decorator from now on...
		$DayGrid->build();
		
		$DayArray = $DayGrid->fetchAll();
		$dda_keys = array_keys($DayArray);
		$start_timestamp = $DayArray[$dda_keys[0]]->gettimestamp();
		$end_timestamp = $DayArray[$dda_keys[count($dda_keys)-1]]->gettimestamp();
		
		//add 86400 because between uses midnight to midnight so the last day needs to be made inclusive
		$events = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp + 86400),"day","","PS",false);
		//print_r($events);
		$this->assign_by_ref("events",$events);
	
		$schedules = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp + 86400),"day_schedule","","PS");
		//print_r($schedules);
		$this->assign_by_ref("sevents",$schedules);
		
		//assigns tablemap and colmap which are matrixs used to figure out how many event in a given increment and what the colspans should be respectively
		$map = $this->build_table_map($incs,$events);
		$this->assign("tablemap",$map['tablemap']);
		$this->assign("tablemap2",$map['tablemap2']);
		$this->assign("maxcols",$map['maxcols']);
		
		$map = $this->build_table_map($incs,$schedules);
		$this->assign("stablemap",$map['tablemap']);
		$this->assign("stablemap2",$map['tablemap2']);
		$this->assign("smaxcols",$map['maxcols']);
		
		$this->assign("start_timestamp",$start_timestamp);
		$this->assign("end_timestamp",$end_timestamp);		
		
		$this->assign("DAY_NEXT_ACTION", $this->_link("day",true) . "date=$ndate");
		$this->assign("DAY_PREV_ACTION", $this->_link("day",true) . "date=$pdate");
		
		$this->assign_by_ref("DayGrid",$DayGrid);
		$this->assign_by_ref("DayArray",$DayArray);
		
		$sidebar = $this->sidebar_action($month."/".$day."/".$year, "day","calendar",$start,$end);
		$this->assign_by_ref("sidebar",$sidebar);
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_day.html");
	}
	
	function day_brief_action($date = "") {
		
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

		// Creata a day as usual
		$Day = new Calendar_Day($year,$month,$day);
	
		// Pass it to the decorator and use the decorator from now on...
		$Day->build();
		$ndate = date("Y-m-d",$Day->nextDay("timestamp")); 
		$pdate = date("Y-m-d",$Day->prevDay("timestamp"));
		$DayArray = array();
		$DayArray[0] = $Day->fetch();
		$dda_keys = array_keys($DayArray);
		$start_timestamp = $DayArray[$dda_keys[0]]->gettimestamp();
		$end_timestamp = $DayArray[$dda_keys[count($dda_keys)-1]]->gettimestamp();
		
		//add 86400 because between uses midnight to midnight so the last day needs to be made inclusive
		$events = Event::get_events_between(date("Y-m-d",$start_timestamp),date("Y-m-d",$end_timestamp + 86400),"day_brief");
		//print_r($events);
		$this->assign_by_ref("events",$events);
		
		$this->assign("start_timestamp",$start_timestamp);
		$this->assign("end_timestamp",$end_timestamp);		
		
		$this->assign_by_ref("Day",$Day);
		$this->assign_by_ref("DayArray",$DayArray);
		
		$this->assign("DAY_NEXT_ACTION", $this->_link("day_brief",true) . "date=$ndate");
		$this->assign("DAY_PREV_ACTION", $this->_link("day_brief",true) . "date=$pdate");
		$sidebar = $this->sidebar_action($month."/".$day."/".$year, "day_brief");
		$this->assign_by_ref("sidebar",$sidebar);
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_day_brief.html");
	}
	
	function sidebar_action($date = "",$view="week_grid",$controller="calendar",$start="",$end="") {
		
		if ($this->_print_view) return ""; 
		
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

                if(count($pa) > 0) {
		$this->assign("rooms_practice_array",$r->rooms_practice_factory($pa[0]->get_id(),false));
		}
		
		$u = new User(null,null);
		$this->assign("users_array",$this->utility_array($u->users_factory("provider"),"id","username"));
		if (isset($_SESSION['calendar']['filters']['user'])) {
			$this->assign("selected_user",$_SESSION['calendar']['filters']['user']);
		}
		if (isset($_SESSION['calendar']['filters']['location'])) {
			$this->assign("selected_location",$_SESSION['calendar']['filters']['location']);
		}
		
		if (isset($_GET['appointment_id']) && is_numeric($_GET['appointment_id'])) {
			$this->assign("edit_app", true);
			$oc = new Occurence($_GET['appointment_id']);
			$sql = "SELECT first_name, last_name from person where person_id =" . $oc->_db->qstr($oc->get_external_id());
			$result = $oc->_db->execute($sql);
			if ($result && !$result->EOF) {
				$this->assign("edit_patient_name", $result->fields['last_name'] . ", " . $result->fields['first_name']);	
			}
			$this->assign("edit_oc",$oc);
		}
		else {
			$oc = new Occurence();
			$oc->date = $date;
			$oc->start = $start;
			$oc->end = $end;
			$this->assign("edit_oc",$oc);
		}
		$this->assign_by_ref("sidebar_months",$months);
		$this->assign("LINK_BASE",$this->_link($view,true));
		$this->assign("appointment_reasons", array_flip($u->_load_enum("appointment_reasons",false)));
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_sidebar.html");
	}
	
	function appointment_popup_action($date="", $start_time = "", $end_time = "", $title = "", $user_id = "", $patient = "") {
		//dont want popup content to be in the trail
		$trail = $_SESSION['trail'];
		array_shift($trail);
		$_SESSION['trail'] = $trail;
		
		$this->sidebar_action();
		
		//grab the oc object the sidebar action initializes. It may already have data in it in the edit case, we will overwrite it because the user did on the page form
		$oc = $this->_tpl_vars['edit_oc'];
		
		if (!empty($date)) {
			$oc->set_date($date);	
		}
		if (!empty($start_time)) {
			$oc->set_start_time($start_time);	
		}
		if (!empty($end_time)) {
			$oc->set_end_time($end_time);	
		}
		//title is actually notes on the oc
		if (!empty($title)) {
			$oc->set_notes($title);	
		}
		if (!empty($user_id)) {
			$oc->set_user_id($user_id);
			$this->assign("selected_user",$user_id);
		}
		//patient is actually external_id on the oc
		if (!empty($patient)) {
			$oc->set_external_id($patient);	
		}
		$this->assign("edit_oc",$oc);
		
		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_appointment_popup.html");
	}
	
	function search_action() {

		$this->assign('search',$_POST);
		$u = new User(null,null);
		$this->assign("providers",$this->utility_array($u->users_factory("provider"),"id","username"));

		$p = new Practice();
		$pa = $p->practices_factory();
		$r = new Room();
		
        if(count($pa) > 0) {
			$this->assign("facility",$r->rooms_practice_factory($pa[0]->get_id(),false));
        }

		return $this->fetch($GLOBALS['template_dir'] . "calendar/" . $this->template_mod . "_search.html");
	}

	function search_action_process() {
		$e = new Event();
		$where = array();
		if (isset($_POST['find_first']) && $_POST['find_first'] == 1 && isset($_POST['provider']) && $_POST['facility']) {
			$sql = "SELECT o.start, o.end from schedules s LEFT JOIN events e on e.foreign_id = s.id LEFT JOIN occurences o on o.event_id = e.id "
				." WHERE s.schedule_code = 'PS' and s.user_id =" .(int)$_POST['provider'];
				 
				$ff_sql = " c.schedule_code = 'PS' and c.user_id =" .(int)$_POST['provider'] . " and o.start BETWEEN '".$e->_mysqlDate($_POST['from'])."' and '"
				.	$e->_mysqlDate($_POST['to'])."' and o.location_id =" . (int)$_POST['facility'];
				$events = $e->get_events($ff_sql,'find_first');
				//var_dump($events);
				
				$ff_sql = " (c.schedule_code != 'PS' or c.schedule_code IS NULL) AND o.user_id = " .(int)$_POST['provider'] . " and o.start BETWEEN '"
				.$e->_mysqlDate($_POST['from'])."' and '".	$e->_mysqlDate($_POST['to'])."' and o.location_id =" . (int)$_POST['facility'];
				$events2 = $e->get_events($ff_sql,'find_first');
				//var_dump($events);
				//var_dump($events2);
				$ffevents = array_diff($events,$events2);
				//var_dump($ffevents);
				$this->assign("free_time", $ffevents);
				$this->assign("APPOINTMENT_ACTION",Cellini::link("day"));
				return;
				/*foreach($ffevents as $free) {
					echo date("m/d/Y", $free) . " from " . date("H:i", $free)  . " to " . date("H:i", $free + 900) . "<br>";	
				}*/
				
		}
		foreach($_POST as $key => $val) {
			if (empty($val)) {
				$key = "noop";
			}
			switch($key) {
				case "from":
				case "to":
					if (!isset($_POST['to'])) {
						$_POST['to'] = date('Y-m-d');
					}
					if (!isset($_POST['from'])) {
						$_POST['from'] = date('Y-m-d');
					}
					if (!isset($where['date_range'])) {
						$where['date_range'] = "o.start BETWEEN '".$e->_mysqlDate($_POST['from'])."' and '".
							$e->_mysqlDate($_POST['to'])."'";
					}
				break;
				case "provider":
					$where[] = 'o.user_id = '.(int)$val;
				break;
				case "patient_id":
					$where[] = 'o.external_id = '.(int)$val;
				break;
				case "facility":
					$where[] = 'o.location_id = '.(int)$val;
				break;
				case "reason":
					$where[] = "o.notes like '%".str_replace("'","",$e->_quote($val)) . "%'";
				break;
				case "schedule_code":
					$where[] = 'schedule_code = '.$e->_quote($val);
				break;
			}	
		}

		$wsql = implode(' and ',$where);
		$events = $e->get_events($wsql,'week');
		
		$this->assign('events',$events);

	}
}

?>
