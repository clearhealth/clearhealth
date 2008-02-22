<?php
$loader->requireOnce('includes/acl/Auth.class.php');

/**
 * UserProfile serves as a way to access "profile" type information relating to a specific users.
 *
 * Examples of use include:
 *  - Returning the user's primary practice ID: ({@link UserProfile::getPrimaryPracticeId()})
 *  - Returning a list of the practice IDs this user has access to ({@link UserProfile::getPracticeIdList()})
 *
 * This should eventually replace {@link Me} as the class to access information about the currently
 * logged in user.  However, it is not limited to only the current user, as it can be initialized
 * with any userId (see {@link UserProfile::UserProfile()}.
 *
 * In general use cases - for accessing information about the currently logged in user - this 
 * class should not be instantiated directly, but rather through 
 * {@link Celini::getCurrentUserProfile()}.
 *
 * @see Celini::getCurrentUserProfile()
 * @package com.clear-health.celini
 * @abstract
 */
class AbstractUserProfile
{
	/**##@+
	 * @access private
	 */
	var $_userId = 0;
	var $_user = null;
	var $_person = null;
	var $_practiceIds = array();
	var $_db = null;
	/**##@-*/
	
	/**
	 * Handle instantiation
	 *
	 * @param int $userId
	 */
	function AbstractUserProfile($userId = 0) {
		$this->_userId = $userId;
		$this->_db =& new clniDB();
	}
	
	/**
	 * Initializes the Person object associated with this
	 *
	 * @access private
	 */
	function _initPerson() {
		if (!is_null($this->_person)) {
			return;
		}
		$this->_initUser();
		$this->_person =& $this->_user->getChild('Person');
	}
	
	/**
	 * Initializes the {@link User} object associated with this
	 *
	 * @access private
	 */
	function _initUser() {
		if (!is_null($this->_user)) {
			return;
		}
		$this->_user =& Celini::newORDO('User', $this->_userId, 'ById');
	}

	function getUserId() {
		return $this->_userId;
	}
}

?>
