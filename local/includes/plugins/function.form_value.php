<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {form_value} plugin
 *
 * Loads the newest value from a form
 *
 * Type:     function<br>
 * Name:     load_patient<br>
 * Input:<br>
 *           - patientId    optional (default is current patient)
 *           - formId
 *           - field
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_form_value($params, &$smarty)
{

        $patientId = 0;
        if (isset($_SESSION[$GLOBALS['config']['app_name']]['controller_vars']['c_patient']['patient_id'])) {
                $patientId = $_SESSION[$GLOBALS['config']['app_name']]['controller_vars']['c_patient']['patient_id'];
        }

	if (isset($params['patientId'])) {
		$patientId = EnforceType::int($params['patientId']);
	}

	$formId = EnforceType::int($params['formId']);

	$db = new clniDb();
	$f = $db->quote($params['field']);
        
	$sql = "
select
concat_ws('',si.value,ss.value,st.value,sd.value) value
from
form_data fd
left join storage_int si on fd.form_data_id = si.foreign_key and si.value_key = $f 
left join storage_string ss on  fd.form_data_id = ss.foreign_key and ss.value_key = $f
left join storage_date sd on fd.form_data_id = sd.foreign_key and sd.value_key = $f
left join storage_text st on fd.form_data_id = st.foreign_key and st.value_key = $f
where
fd.form_id = $formId and
(
/* form is directly tied to the patient */
external_id = $patientId

/* form is tied to a encounter of the patient */
or external_id in (select encounter_id from encounter where patient_id = $patientId)
)
ORDER BY fd.last_edit DESC
limit 1
	";

	return $db->getOne($sql);
}
?>
