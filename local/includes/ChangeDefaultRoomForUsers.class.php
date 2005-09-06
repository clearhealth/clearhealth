<?php

/**
 * Iterates across a list of Users and sets their default room.
 *
 * Though ORDO is not currently a visitable object, this acts like a 
 * quasi-Visitor.
 */
class ChangeDefaultRoomForUsers
{
	var $_room_id;
	
	/**
	 * Handle initialization
	 *
	 * @param object
	 */
	function ChangeDefaultRoomForUsers(&$room) {
		$this->_room_id = $room->get('id');
	}
	
	/**
	 * Visits a list of users.
	 *
	 * @param array
	 *
	 * @todo Implement {@link UserList} object that is visitable and redirect
	 *    all direct calls to this visit() through it's accept() method.
	 */
	function visit($userList) {
		assert('is_array($userList)');
		
		foreach ($userList as $user) {
			$user->set('default_location_id', $this->_room_id);
			$user->persist();
		}
	}
}

