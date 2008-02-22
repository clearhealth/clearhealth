<?php

/**
 * Controller to hold stuff that a user can edit on there account
 * @package	com.clear-health.clearhealth
 */
class C_MyAccount extends Controller {

	/**
	 * Update the password of the currently logged in user
	 */
	function password_action_view() {
		$user =& $this->_me->get_user();

		$this->assign_by_ref('user',$user);

		$this->view->path = 'user';
		return $this->view->render('password.html');
	}

	function password_action_process() {
		$user =& $this->_me->get_user();

		if ($_POST['password']['current_password'] !== $user->get('password')) {
			$this->messages->addMessage('Current Password Incorrect');
			return "";
		}
		$user->set('password',$_POST['password']['password']);
		$user->persist();
		$this->messages->addMessage('Password Changed');
	}
}
?>
