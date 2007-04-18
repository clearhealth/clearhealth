<?php

require_once dirname(__FILE__) . '/altAlertList.abstract.php';

class altAlertListByTypeAndName extends altAlertList
{
	var $_results = array();
	
	/**
	 * @param  string  The owner_type
	 * @param  string  The owner_id (name)
	 */
	function altAlertListByTypeAndName($type = null, $name = null) {
		$db =& new clniDB();
		$qType = $db->quote($type);
		$qName = $db->quote($name);
		$sql = "
			SELECT
				altnotice_id 
			FROM
				altnotice
			WHERE
				owner_type = {$qType} AND
				owner_id = {$qName} AND
				(completed_date > NOW() OR completed_date = '0000-00-00 00:00:00') AND
				deleted = 0
			ORDER BY due_date DESC";
		$this->_results = $db->execute($sql);
	}
}

