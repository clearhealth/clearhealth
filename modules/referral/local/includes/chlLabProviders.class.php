<?php

class chlLabProviders
{
	function toArray() {
		$db =& new clniDB();
		$sql = '
			SELECT SQL_CACHE 
				lab_provider 
			FROM
				' . chlUtility::chlCareTable('clinic_group_lab_providers') . '
			WHERE 
				1
				' .
				($_SESSION['sLoggedInClinicGroupName'] != "0" ?
					'AND clinic_group = ' . $db->quote($_SESSION['sLoggedInClinicGroupName']) :
					'GROUP BY lab_provider') . '  
			ORDER BY
				lab_provider';
		$results = $db->execute($sql);
		$return = array();
		while (!$results->EOF) {
			$return[$results->fields['lab_provider']] = $results->fields['lab_provider'];
			$results->moveNext();
		}
		return $return;
	}
}

