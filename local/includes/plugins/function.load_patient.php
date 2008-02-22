<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
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
 *           - patientId    optional (default is current patient)
 *           - variable     optional (default is patient)
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_load_patient($params, &$smarty)
{

        $patientId = 0;
        if (isset($_SESSION[$GLOBALS['config']['app_name']]['controller_vars']['c_patient']['patient_id'])) {
                $patientId = $_SESSION[$GLOBALS['config']['app_name']]['controller_vars']['c_patient']['patient_id'];
        }

	if (isset($params['patientId'])) {
		$patientId = EnforceType::int($params['patientId']);
	}

	$variable = 'patient';
	if (isset($params['variable'])) {
		$variable = $params['variable'];
	}
        

	$smarty->_tpl_vars[$variable] = Celini::newOrdo('Patient',$patientId);
}
?>
