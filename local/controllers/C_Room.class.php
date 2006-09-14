<?php
$loader->requireOnce('includes/colorpickerselect.class.php');

class C_Room extends Controller
{
	var $_ordo = null;
	
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	function actionEdit($id = 0) {		
		if (!is_object($this->_ordo)) {
			$this->_ordo =& Celini::newORDO('Room', $id);
		}
		
		$this->assign("room",$this->_ordo);
		$b =& Celini::newORDO('Building');
		$buildings = $b->valueList();
		if($buildings == false || count($buildings) == 0) {
			$this->messages->addMessage('You must add a building to a practice before adding any rooms.');
			return;
		}

		$this->assign("buildings",$b->valueList());

		$picker =& new colorPickerSelect('pastels','color','','#'.$this->_ordo->get('color'));
		$this->view->assign_by_ref('colorpicker',$picker);

		$this->assign("process",true);
		$this->view->assign('FORM_ACTION', Celini::link('edit', 'Room', true, $this->_ordo->get('id')));
		return $this->view->render("edit.html");
	}
	
		
	function processEdit() {
		// Capture so we know whether or not this was the first room
		$room =& Celini::newORDO('Room');
		$setDefaultRoom = !$room->roomsExist();
		
		// Check and if allowed handle the saving	
		$room->setup($_POST['id']);
		$room->populateArray($_POST);
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
