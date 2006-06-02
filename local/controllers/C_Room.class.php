<?php
$loader->requireOnce('ordo/Building.class.php');

class C_Room extends Controller
{
	var $_ordo = null;
	
	function C_Room() {
		parent::Controller();
		$this->view->path = 'location';
	}
	
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	function actionEdit($id = 0) {		
		if (!is_object($this->_ordo)) {
			$this->_ordo =& Celini::newORDO('Room', $id);
		}
		
		$this->assign("room",$this->_ordo);
		$b = new Building();
		$this->assign("buildings",$this->utility_array($b->buildings_factory(),"id","name"));

		$this->assign("process",true);
		$this->view->assign('FORM_ACTION', Celini::link('edit', 'Room', true, $this->_ordo->get('id')));
		return $this->view->render("edit_room.html");
	}
	
		
	function processEdit() {
		// Capture so we know whether or not this was the first room
		$room =& Celini::newORDO('Room');
		$setDefaultRoom = !$room->roomsExist();
		
		// Check and if allowed handle the saving	
		$room->set('id', $_POST['id']);
		$room->populate_array($_POST);
		$room->persist();
		
		// If no rooms were set prior to creating this one, utilize the pseudo
		// visitor ChangeDefaultRoomForUsers() to update the default rooms.
		if ($setDefaultRoom) {
			$GLOBALS['loader']->requireOnce('includes/ChangeDefaultRoomForUsers.class.php');
			$updater =& new ChangeDefaultRoomForUsers($room);
			
			$user =& ORDataObject::factory('User');
			$updater->visit($user->users_factory());
		}
		
		// share this object with the rest of the controller so the DB doesn't
		// have to be requeried.
		$this->_ordo =& $room;
	}
}
