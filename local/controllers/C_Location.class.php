<?php

require_once CELINI_ROOT."/controllers/Controller.class.php";
//require_once APP_ROOT . "/local/controllers/C_Main.class.php";
require_once APP_ROOT . "/local/controllers/C_Schedule.class.php";

require_once APP_ROOT . "/local/ordo/Practice.class.php";
require_once APP_ROOT . "/local/ordo/Building.class.php";
require_once APP_ROOT . "/local/ordo/Room.class.php";
require_once APP_ROOT . "/local/ordo/Schedule.class.php";
require_once APP_ROOT . "/local/ordo/FacilityCode.class.php";


class C_Location extends Controller {

	var $template_mod;
	var $location;

	function C_Location($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
		$this->assign("FORM_ACTION", Celini::link(true) . $_SERVER['QUERY_STRING']);
		$this->assign("TOP_ACTION", Celini::link(true));

		$this->assign('EDIT_SCHEDULE_ACTION', Celini::link('edit_schedule'));
		$this->assign('DELETE_ACTION', Celini::link('delete'));
		$this->assign('EDIT_PRACTICE_ACTION', Celini::link('edit','practice'));
		$this->assign('EDIT_BUILDING_ACTION', Celini::link('edit_building'));
		$this->assign('EDIT_ROOM_ACTION', Celini::link('edit_room'));
		$this->assign('EDIT_EVENT_ACTION', Celini::link('edit_event'));
		$this->assign('EDIT_WIZARD_ACTION', Celini::link('edit_schedule','personSchedule'));
		$this->assign('SCHEDULE_LIST_ACTION', Celini::link('schedule_list'));
		$this->assign('UPDATE_SCHEDULE_ACTION', Celini::link('update_schedule'));
		$this->assign('EDIT_TIMEPLACE_ACTION', Celini::link('edit_timeplace'));
	}

	function default_action() {
		return $this->edit_action();
	}

	function list_action() {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		
		$s = new Practice();
		$this->assign("practices",$s->practices_factory());
		$b = new Building();
		$this->assign("buildings",$b->buildings_factory());
		$r = new Room();
		$this->assign("rooms",$r->rooms_factory());
		
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_list.html");
	}

	// todo: move to a different controller
	function schedules_action() {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		$c = new Schedule();
		$this->assign("schedules",$c->schedules_factory());
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_schedules.html");
	}
	
	function schedule_list_action() {
		
		$c = new Schedule();
		$this->assign("schedules",$c->schedules_factory());
		
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_schedule_list.html");
	}

	function edit_building_action($id = "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","building",$this,false);
		if (!is_object($this->location)) {
			$this->location = new Building($id);
		}
		
		$this->assign("building",$this->location);
		$s = new Practice();
		$this->assign("practices",$this->utility_array($s->practices_factory(),"id","name"));
		
		$fc = &new FacilityCode();
		$this->assign('facilityCodeList', $fc->valueListForDropDown()); 

		$this->assign("process",true);
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_edit_building.html");
	}
	
		
	function edit_building_action_process() {
		if ($_POST['process'] != "true") {
			return;
		}
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","building",$this,false);	
		$this->location = new Building($_POST['id']);
		$this->location->populate_array($_POST);
		$this->location->set('identifier',$_POST['identifier']);
		
		$this->location->persist();
		
		$this->location->populate($this->location->get_id());
		$_POST['process'] = "";
	}
	
	function edit_room_action($id = "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","room",$this,false);
		
		if (!is_object($this->location)) {
			$this->location = new Room($id);
		}
		
		$this->assign("room",$this->location);
		$b = new Building();
		$this->assign("buildings",$this->utility_array($b->buildings_factory(),"id","name"));

		$this->assign("process",true);
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_edit_room.html");
	}
	
		
	function edit_room_action_process() {
		if ($_POST['process'] != "true") {
			return;
		}
		
		// Capture so we know whether or not this was the first room
		$room =& new Room();
		$setDefaultRoom = !$room->roomsExist();
		
		// Check and if allowed handle the saving
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","room",$this,false);	
		$location =& $room;
		$location->set('id', $_POST['id']);
		$location->populate_array($_POST);
		$location->persist();
		
		$_POST['process'] = "";
		
		// If no rooms were set prior to creating this one, utilize the pseudo
		// visitor ChangeDefaultRoomForUsers() to update the default rooms.
		if ($setDefaultRoom) {
			include_once APP_ROOT . '/local/includes/ChangeDefaultRoomForUsers.class.php';
			$updater =& new ChangeDefaultRoomForUsers($location);
			
			$user =& ORDataObject::factory('User');
			$updater->visit($user->users_factory());
		}
		
		// share this object with the rest of the controller so the DB doesn't
		// have to be requeried.
		$this->location =& $location;
		
		//creat an event for a new room
		if ($_POST['id'] == 0)
			$this->_populateevents($location);
	}
	
	
	function _populateevents($room) {
		//get a list of current schedules
		$schedule =& celini::newOrdo('schedule');
		$schedules = $schedule->schedules_factory();
		//loop over the schedules
		foreach ($schedules as $s) {
			echo($s->get('name'));	
		//add an event group named after the room inside each schedule			
			$e =& ORDataObject::factory('Event');
			$e->set('title',$room->get('name'));
			$e->set('foreign_id',$s->get('id'));
			$e->persist();
		}
	}	
	
	function edit_schedule_action($id = "") {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		if (!is_object($this->location)) {
			$this->location = new Schedule($id);
		}
		
		$this->assign("schedule",$this->location);
		$r = new Room();
		$this->assign("rooms_practice_array",$r->rooms_practice_factory($this->location->get_practice_id()));
		$s = new Practice();
		$this->assign("practices",$this->utility_array($s->practices_factory(),"id","name"));
		
		$pa = $s->practices_factory();
		$r = new Room();
		if(count($pa) > 0) {
			$this->assign("rooms_practice_array",$r->rooms_practice_factory($pa,false));
		}
		
		$u = new User(null,null);
		$this->assign("users_array",$this->utility_array($u->users_factory("provider"),"id","username"));
		
		if (!isset($this->_tpl_vars['edit_event']))	$this->assign("edit_event",new Event());
		if (!isset($this->_tpl_vars['edit_timeplace']))	$this->assign("edit_timeplace",new Occurence());
		$this->assign("process",true);
		$this->assign("EVENT_ACTION", Celini::link("edit_event") . "id=$id");
		$this->assign("OCCURENCE_ACTION", Celini::link("edit_occurence") . "id=$id");
		$this->assign("SELECTED_ACTION", Celini::link("selected_occurence") . "id=$id");
		//$this->assign("OCCURENCE_ACTION", "controller.php?" . str_replace("edit_schedule","edit_occurence",$_SERVER['QUERY_STRING']));
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_edit_schedule.html");
	}

	function selected_occurence_action($id) {
	}

	function selected_occurence_action_process($id) {
		if ($_POST['action'] == 'delete' && count($_POST['selected']) > 0) {
			foreach($_POST['selected'] as $oid => $val) {
				$action = "delete";
				$o = new Occurence($id);
				$o->populate();
				if($o->get_last_change_id() == $this->_me->get_user_id()){
					$action = 'delete_owner';
				}else{
					$action = 'delete';
				}

				$this->sec_obj->acl_qcheck($action,$this->_me,"",'occurence',$this,false);
		
				$error = true;
			
				$obj = null;
			
				$obj = new Occurence($oid);
				$message = '';
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

				$this->assign("message", $message);
				if ($error) {
					$this->assign("error",true);
					$this->_state = false;
					return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_delete.html");	
				}
			}
		
			$next_url =  Celini::link('edit_schedule',true,true);
			//if the object was deleted then we cannot refer to it...
		}else{
			$next_url = Celini::link('edit_schedule',true,true,$id);
			//if the object was not deleted then we should keep it in focus...
		}
		header('Location: '.$next_url);
	}
	
	function edit_schedule_action_process() {
		if ($_POST['process'] != "true")
			return;
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		$this->location = new Schedule($_POST['id']);
		$this->location->populate_array($_POST);
		
		$this->location->persist();
		
		$this->location->populate();
		$_POST['process'] = "";
		$this->_state = false;
		return $this->edit_schedule_action($this->location->get_id());
	}
	
	function edit_event_action($id = "", $fid= "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		$this->assign("edit_event",new Event($id));
		
		return $this->edit_schedule_action($fid);
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
		
		$this->location->populate();
		$_POST['process'] = "";
		$this->_state = false;
		$this->location = null;
		header("Location: ".Celini::link("edit_schedule").$_SERVER['QUERY_STRING']);
		return $this->edit_schedule_action($schedule_id);
	}
	
	function edit_appointment_action_process($event_id = "",$confirm =false) {
		if (!isset($_POST['occurence_id'])) {
			$_POST['occurence_id'] = 0;
		}
		$oc = new Occurence($_POST['occurence_id']);
		if($this->_me->get_user_id() == $oc->get_user_id()) {
			$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		}
		else {
			$this->sec_obj->acl_qcheck("edit_owner",$this->_me,"","event",$this,false);
		}
		
		$_POST['process'] = "";
		$this->_state = false;
		
		if (is_array($event_id)) {
			$this->event = new Event();
			$this->event->populate_array($event_id);
		}
		else {
			$this->event = new Event($event_id,false);
			$this->event->populate_array($_POST);
		}

		$users = array();
		if (isset($_POST['users']) && count($_POST['users']) > 0) {
			$tmp =  $_POST['users'];
			$_POST['user_id'] = array_shift($tmp);
			$users = $_POST['users'];
		}
		
		$oc->populate_array($_POST);

		$cs = new C_Schedule();
		//check for availability of provider
		if (is_numeric($oc->get_user_id())) {
			$availability = $cs->check_availability($oc, $this->event);
			if ($availability) {
				//echo "this event is within providers schedule<br>";	
			}
			else {
				//echo "this event is NOT within providers schedule<br>";
			}
		}

		// get a Appointment template from the reason
		$manager =& EnumManager::getInstance();
		$list =& $manager->enumList('appointment_reasons');
		$reason = false;
		for($list->rewind();$list->valid();$list->next()) {
			$row = $list->current();
			if ($row->key == $this->POST->get('reason_id')) {
				$reason = $row;
			}
		}
		if ($reason && $reason->extra1 !== '') {
			$template = Celini::newOrdo('AppointmentTemplate',$reason->extra1);
		}
		else {
			$template = Celini::newOrdo('AppointmentTemplate');
		}

		// check for the walkin flag
		$walkin = 0;
		if (isset($_POST['walkin'])) {
			$walkin = $_POST['walkin'];
		}

		//check for double book
		$double = false;
		if (is_numeric($oc->get_user_id()) && $_POST['occurence_id'] < 1 && $walkin != 1) {
			$double = $cs->check_double_book($oc,$this->event,$template->breakdownSum($users));
			if ($double) {
				if(!$this->sec_obj->acl_qcheck("double_book",$this->_me,"","event",$this,true)) {
					echo "The event you are trying to add collides with another event. You do not have permission to double book events. You can use the back button of your browser to alter the event so that it does not collide and try again.";
					exit;
	            	}
				//echo "this event is double booking<br>";	
			}
			else {
				//echo "this event is NOT double booking<br>";
			}
		}
		
		if (!($availability && !$double) && !$confirm) {
			return $cs->confirm_action($_POST);	
		}
		
		$this->event->persist();
		$this->event->populate();
		
		$oc->set_event_id($this->event->get_id());
		if (isset($_POST['walkin'])) {
			$oc->set('walkin',$_POST['walkin']);
		}
		if (isset($_POST['group_appointment'])) {
			$oc->set('group_appointment',$_POST['group_appointment']);
		}
		if (isset($_POST['reason_id'])) {
			$oc->set('reason_code',$_POST['reason_id']);
		}

		$oc->persist();
		$oc->populate();


		if (isset($_POST['users'])) {
			$template->fillTemplate($oc->get('id'),$_POST['users']);
		}
		else {
			$template->resetTemplate($oc->get('id'));
		}

		
		$this->location = null;

		$trail =& Celini::trailInstance();
		$trail->skipActions = array('edit_appointment','confirm','find','appointment_popup');
		$action = $trail->lastItem();
		header("Location: " . $action->link());
		exit;
	}
	
	
	function edit_occurence_action_process($schedule_id = "") {
		if ($_POST['process'] != "true")
			return;
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
		$id = "";
		if (isset($_POST['id'])) $id =$_POST['id'];
		$this->location = new Occurence($id);
		$this->location->populate_array($_POST);
		$this->location->persist();
		
		$this->location->populate($this->location->get_id());
		$_POST['process'] = "";
		$this->_state = false;
		$this->location = null;
		header("Location: " . Celini::link("edit_schedule") . "id=" . $_POST['schedule_id']);
		return;
	}
	
	function delete_action($id = "",$object_class ="") {
		
		$action = "delete";
		if($object_class == "occurence"){
			$o = new Occurence($id);
			$o->populate();
			if($o->get_last_change_id() == $this->_me->get_user_id()){
				$action = 'delete_owner';
			}
			else{
				$action = 'delete';
			}
		}

		$this->sec_obj->acl_qcheck($action,$this->_me,"",$object_class,$this,false);
		
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
		$this->assign("DELETE_ACTION", Celini::link('delete')."id=$id&object_class=$object_class");
		return $this->fetch($GLOBALS['template_dir'] . "locations/" . $this->template_mod . "_delete.html");
	}
	
	function delete_action_process($id = "",$object_class ="") {

		$action = "delete";
		if($object_class == "occurence"){
			$o = new Occurence($id);
			$o->populate();
			if($o->get_last_change_id() == $this->_me->get_user_id()){
				$action = 'delete_owner';
			}else{
				$action = 'delete';
			}
		}

		$this->sec_obj->acl_qcheck($action,$this->_me,"",$object_class,$this,false);
	
		if ($_POST['process'] == true && (isset($_POST['cancel']) || isset($_GET['cancel']))) {
			$this->_redirLast();
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

		$this->_redirLast();
	}

	function _redirLast() {
		$trail =& Celini::trailInstance();
		$trail->skipActions = array('delete');
		$action = $trail->lastItem();
		header("Location: ".$action->link());
		exit;
	}

	function update_schedule_action_process($id = "",$schedule_code ="",$group_name ="") {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
	
		if ($_POST['process'] != "true") {
			return;
		}

		if(empty($id) && empty($object_class)) {
			$id = $_POST['id'];	
		}
		
		if (is_numeric($id) && !empty($schedule_code) && !empty($group_name)) {
			
			$oc = new Occurence($id);
			$s = new Schedule();
			$sa = $s->schedules_factory();
			
			foreach ($sa as $schedule) {
				if ($schedule->get_schedule_code() == $schedule_code) {
					$ea = $schedule->get_events();
					foreach ($ea as $event) {
						if ($event->get_title() == urldecode($group_name)) {
							$oc->set_event_id($event->get_id());
							$oc->persist();
							break;	
						}	
					}
					break;	
				}	
			}	
			
		}

		$location = Celini::link('list','location');
		$trail =& Celini::trailInstance();
		$trail->skipActions = array('update_schedule');
		$action = $trail->lastItem();
		header("Location: ".$action->link());
		exit;
	}

	function edit_practice_action($id = 0) {
		header('Location: '.Celini::link('edit','Practice',true,$id));
	}
}
?>
