<?php

$loader->requireOnce("ordo/ORDataObject.class.php");
$loader->requireOnce("includes/Email.class.php");

/**
*	Controller class the handles the default case of access that leads to login
*	Extends {@link Controller} see that for additional implementation and guideline details
*/
class C_Base_Access extends Controller {

	function C_Access ($template_mod = "general") {
		parent::Controller($template_mod);
	}

	function default_action_view() {
		if ($this->_me->get_id() > 0) {
			Celini::redirectDefaultLocation();
		}
		else {
			header("Location: ".Celini::link('login','access','main'));
		}
		exit;
		//return $this->login_action();
	}
	
	// there is no way to put an acl here, or you won't be able to view the login page
	function login_action() {
		$this->assign('FORM_ACTION',Celini::link('login','access'));
		$this->assign('FORGOT_ACTION',Celini::link('forgot','access'));
		return $this->view->render('login.html');	
	}
	
	function logout_action() {
		session_destroy();
		//var_dump($_SESSION);
		header("Location: ".Celini::link('login','access'));
		exit;
		//return $this->login_action();
	}
	
	function denied_action() {
		return $this->view->render('denied.html');	
	}
	
	function login_action_process() {
		if ($_POST['process'] !== "true") {
			return;
		}
		$status = false;
		if (isset($_POST['username']) && isset($_POST['password'])) {
			$status = $this->login($_POST['username'],$_POST['password']);
		}
		if ($status) {
			if (	isset($GLOBALS['config']['maintenanceMode']) &&
				$GLOBALS['config']['maintenanceMode'] === 'splash' &&
				!$_SESSION['clicked_through_splash'] == true) {
				
				header ("Location: ".Celini::link('splash','Access'));
				exit;
			}
			header ("Location: ".Celini::link('default','Access'));
			exit;
		}
		else {
			$this->messages->addMessage("The information supplied could not be used to lookup a user, please try again.");
			if (!isset($_POST['username'])) $_POST['username'] = "";
			$this->assign("username",$_POST['username']);
			
			return $this->denied_action();
		}
	}

	/**
	 * Perform the actual login, setup the session, return a bool
	 */
	function login($username,$password) {
		//login user against user table
		$u =& ORDataObject::factory('User',$username,$password);
		
		if ($u->get_id() == DEFAULT_USER_ID) {
			$this->_state = false;
			return false;
		}
		elseif (is_numeric($u->get_id())) {
			$this->_me->set_user($u);	
		}
		
		$ct = new PreferenceTree("9000");
		$_SESSION['prefs']['default'] = $ct;
		
		$ct = new PreferenceTree($u->get_id());
		$_SESSION['prefs']['user'] = $ct;

		$this->login_session_setup($u);

		$this->_state = false;
		return true;
	}

	/**
	 * User hook function for extra session setup at login
	 */
	function login_session_setup() {
	}

	function forgot_action() {
		$this->assign('FORM_ACTION',Celini::link('forgot','access'));
		if (!isset($this->submitted)) {
			return $this->view->render('forgot.html');	
		}
	}

	function forgot_action_process() {

		ORDataObject::factory_include('User');
		$user =& User::fromUsername($_POST['username']);
		$person =& ORDataObject::Factory('Person',$user->get('person_id'));

		if ($user->get_user_id() == 0) {
			$this->messages->addMessage("Unknown Username: $_POST[username]","");
		}
		else {
			$this->submitted = true;

			$pwreset =& ORDataObject::factory('Password_reset');
			$pwreset->set('submitter_ip',$_SERVER['REMOTE_ADDR']);
			$pwreset->set('user_id',$user->get('user_id'));
			$pwreset->persist();

			$email = new Email();
			$email->subject = "Password Reset";
			$email->addresses[] = $person->get('email');
			$email->addresses[] = $person->get('secondary_email');

			$this->assign('RESET_ACTION',"http://".$_SERVER['HTTP_HOST'].Celini::link('reset_password','access','main',$pwreset->get('hash')));
			$email->body = $this->fetch(Celini::getTemplatePath("/access/reset_email.txt"));

			$email->prepare();
			
			if (!$email->send()) {
				var_dump($email->error);
			}

			return $this->view->render('forgot_submitted.html');	
		}
	}

	function reset_password_action($hash=false) {
		
		ORDataObject::factory_include('Password_reset');

		$pwreset =& Password_reset::fromHash($hash);
		if ($pwreset->get('id') > 0) {
			return $this->view->render('reset_password_error.html');	
		}
		$this->assign('FORM_ACTION',Celini::link('reset_password',true,true,$hash));
		$this->assign('PROCESS','true');
		if (isset($this->error)) {
			return "";
		}
		return $this->view->render('reset_password.html');	
	}

	function reset_password_action_process($hash = false) {
		ORDataObject::factory_include('Password_reset');
		$pwreset =& Password_reset::fromHash($hash);
		if ($pwreset->get('id') == 0) {
			$this->error = true;
			return $this->view->render('reset_password_error.html');	
		}

		ORDataObject::factory_include('user');
		$user =& User::fromId($pwreset->get('user_id'));
		if ($user->get('id') > 0) {
			$user->populate_array($_POST);
			$user->persist();
		}

		$pwreset->drop();
		location('Location: '.Celini::link('login','access'));
		die();
	}
	function actionSplash() {
		$ordo = ORDataObject::factory("Splash",0);
		$this->view->assign("ordo",$ordo);
		return $this->view->render('splash.html');
	}
	function processSplash()  {
		$_SESSION['clicked_through_splash'] = true;
		header ("Location: ".Celini::link('default','Access'));
                exit;
	}
}
?>
