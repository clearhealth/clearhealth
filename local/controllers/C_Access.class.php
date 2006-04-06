<?php

$loader->requireOnce('controllers/C_Base_Access.class.php');
$loader->requireOnce('controllers/C_Calendar.class.php');
$loader->requireOnce('controllers/M_Calendar.class.php');

class C_Access extends C_Base_Access {
                                                                                
	// todo: figure out a generic way to do this
        function login_session_setup(&$user) {
		if ($this->security->acl_qcheck('usage',false,'resources','calendar')) {
			$c = new C_Calendar();
			$m = new M_Calendar();
			$m->setController($c);
			$m->process_setFilter("","location/" . $user->get("default_location_id"));
		}
		else {
			header('Location: '.Celini::link('find','PatientFinder'));
			die();
		}
        }
}

?>
