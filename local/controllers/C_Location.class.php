<?php

//$loader->requireOnce('controllers/C_Main.class.php');
$loader->requireOnce('controllers/C_Schedule.class.php');

$loader->requireOnce('ordo/Practice.class.php');
$loader->requireOnce('ordo/Building.class.php');
$loader->requireOnce('ordo/Room.class.php');
$loader->requireOnce('ordo/Schedule.class.php');
$loader->requireOnce('ordo/FacilityCode.class.php');


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

	function actionList() {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		
		$s = new Practice();
		$this->assign("practices",$s->practices_factory());
		$b = new Building();
		$this->assign("buildings",$b->buildings_factory());
		$r = new Room();
		$this->assign("rooms",$r->rooms_factory());
		
		return $this->view->render("list.html");
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
		return $this->view->render("delete.html");
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
			return $this->view->render("delete.html");
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

	function edit_practice_action($id = 0) {
		header('Location: '.Celini::link('edit','Practice',true,$id));
	}
}
?>
