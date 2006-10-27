<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {load_patient} plugin
 *
 * Loads patient data into a smart variable from within a template
 *
 * Type:     function<br>
 * Name:     load_patient<br>
 * Input:<br>
 *           - userId    optional (default is currently logged in user)
 *           - userVariable     optional (default is user)
 *           - userPersonVariable     optional (default is userperson)
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_load_user($params, &$smarty)
{
	$profile =& Celini::getCurrentUserProfile();

        $userId = $profile->getUserId();
	if (isset($params['userId'])) {
		$userId = EnforceType::int($params['userId']);
	}

	$userVariable = 'user';
	if (isset($params['userVariable'])) {
		$userVariable = $params['userVariable'];
	}

	$userPersonVariable = 'userperson';
	if (isset($params['userPersonVariable'])) {
		$userPersonVariable = $params['userPersonVariable'];
	}
        

	$smarty->_tpl_vars[$userVariable] =& Celini::newOrdo('User',array($userId),'ById');
	$smarty->_tpl_vars[$userPersonVariable] =& Celini::newOrdo('Person',$smarty->_tpl_vars[$userVariable]->get('person_id'));
}
?>
