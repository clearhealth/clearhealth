<?php

require_once CELLINI_ROOT.'/controllers/C_Base_Access.class.php';
require_once APP_ROOT.'/local/controllers/C_Calendar.class.php';
require_once APP_ROOT.'/local/controllers/M_Calendar.class.php';

class C_Access extends C_Base_Access {
                                                                                
        function login_session_setup(&$user) {
        	
        	$c = new C_Calendar();
            $m = new M_Calendar();
            $m->setController($c);
        	$m->process_setFilter("","location/" . $user->get("default_location_id"));
        }
}

?>