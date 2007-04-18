<?php

class chlUtility
{
	/**
	 * Sets up the information that is required for the patient context menu to
	 * display for a given <i>$patient_id</i>.
	 *
	 * @param int
	 */
	function loadPatientInfo($patient_id) {
		global $loader;
		if ($loader->requireOnce('includes/chlPatientTopInfoMapper.class.php')) {
			$mapper =& new chlPatientTopInfoMapper((int)$patient_id);
		}
	}
	
	
	/**
	 * Return the full database.table string for a CHLCare table.
	 *
	 * @param  string  The name of the table
	 * @return string
	 */
	function chlCareTable($table) {
		if (!is_null($chlcareConfig = $GLOBALS['configObj']->get('chlcare'))) {
			return $chlcareConfig['db_name'] . '.' . $table;
		}
		return $table;
	}
	
	
	/**
	 * Returns a PHP array of the possible values of an enum/set in a MySQL database.
	 *
	 * @param  string  Name of field
	 * @param  string  Name of table field is from
	 * @return array
	 */
	function enumToArray($name, $fromTable) {
		$db =& new clniDB();
		$sql = 'SHOW COLUMNS FROM ' . chlUtility::chlCareTable($db->escape($fromTable)) . ' LIKE ' . $db->quote($name);
		$result = $db->execute($sql, ADODB_FETCH_NUM);
		$options = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $result->fields[1]));
		return $options;
	}
}
