<?php

class chlProviderArray
{
	var $_db = null;
	var $_corral = array();
	var $_clinicGroupName = null;
	
	function chlProviderArray($clinicGroupName = null) {
		$this->_db =& new clniDB();
		$this->_clinicGroupName = $clinicGroupName;
	}
	
	function toArray() {
		$this->_init();
		return $this->_corral;
	}
	
	function _init() {
		if (count($this->_corral) > 0) {
			return;
		}
		$sql = '
			SELECT 
				user_id,
				CONCAT(
					CONCAT_WS(" ", p.first_name, p.middle_name, p.last_name),
					" -> ", c.name
				)
					AS provider_name
			FROM
				user  AS u
				INNER JOIN person p on p.person_id = u.person_id
				INNER JOIN practices AS c on p.primary_practice_id = c.id
			WHERE
				u.disabled = 2
				AND c.id = ' . $this->_db->quote($this->_clinicGroupName)  . '
			ORDER BY
				provider_name';
		$results = $this->_db->execute($sql);
		
		while ($results && !$results->EOF) {
			$this->_corral[$results->fields['user_id']] = $results->fields['provider_name'];
			$results->moveNext();
		}
	}
}
