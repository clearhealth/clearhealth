<?php

$loader->requireOnce('controllers/C_Base_Access.class.php');

class C_Access extends C_Base_Access {
                                                                                
	// todo: figure out a generic way to do this
        function login_session_setup(&$user) {
		if ($this->security->acl_qcheck('usage',false,'resources','calendar')) {
		}
		else {
			header('Location: '.Celini::link('find','PatientFinder'));
			die();
		}
        }
}

?>
