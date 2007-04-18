<?php

require_once dirname(__FILE__) . '/altAlertList.abstract.php';

class altPersonalAlertList extends altAlertList
{
	function altPersonalAlertList($user_id) {
		$db =& new clniDB();
		$sql = sprintf('
			SELECT altnotice_id FROM altnotice WHERE 
				(
					(owner_type = "User" AND owner_id = "%d") OR
					(owner_type = "Patient")
				) AND
				due_date <= NOW() AND
				deleted = 0
			ORDER BY due_date DESC, altnotice_id DESC',
			$user_id);
		$this->_results = $db->execute($sql);
	}
}
