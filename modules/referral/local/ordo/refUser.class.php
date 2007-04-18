<?php

class refUser extends ORDataObject
{
	var $refuser_id = '';
	var $external_user_id = '';
	var $refusertype = '';
	var $refprogram_id = '';
	var $deleted = 0;
	
	/**#@+
	 * {@inheritdoc}
	 */
	var $_key = 'refuser_id';
	var $_table = 'refuser';
	/**#@-*/
	
	function setupByExternalUserId($user_id) {
		$user_id = EnforceType::int($user_id);
		$this->set('external_user_id', EnforceType::int($user_id));
		parent::populate('external_user_id');
	}
	
	function setupByUserAndProgramId($user_id, $refprogram_id) {
		$sql = '
			SELECT * FROM ' . $this->_table . ' 
			WHERE external_user_id = ' . (int)$user_id . ' AND refprogram_id = ' . (int)$refprogram_id . '
			ORDER BY deleted ASC';
		$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
	}
	
	function setupByUserAndType($user_id, $refusertype) {
		$em =& Celini::enumManagerInstance();
		$sql = '
			SELECT
				*
			FROM 
				' . $this->_table . ' AS ru
				' . $em->joinSql('refUserType', 'ru.refusertype') . '
			WHERE
				refUserType.value = ' . $this->dbHelper->quote($refusertype) . ' AND
				ru.external_user_id = ' . $this->dbHelper->quote($user_id) . '
				ORDER BY deleted ASC';
		$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
	}


	function setupByUserTypeAndProgramId($user_id, $type, $refprogram_id) {
		$em =& Celini::enumManagerInstance();
		$sql = '
			SELECT
				*
			FROM 
				' . $this->_table . ' AS ru
				' . $em->joinSql('refUserType', 'ru.refusertype') . '
			WHERE
				refUserType.value = ' . $this->dbHelper->quote($type) . ' AND
				ru.external_user_id = ' . $this->dbHelper->quote($user_id) . ' AND
				ru.refprogram_id = ' . $this->dbHelper->quote($refprogram_id) . '
				ORDER BY deleted ASC';
		$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
	}


	
	/**
	 * Now an alias to <i>refUser->value('refusertype')</i>.
	 *
	 * @deprecated
	 */
	function get_refusertype_text() {
		return $this->value('refusertype');
	}
	
	function value_refusertype() {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('refUserType', $this->get('refusertype'));
	}
	
	function value_username() { 
		$person =& Celini::newORDO('Person', $this->get('external_user_id'));
		return $person->value('username');
	}
	
	function value_clinicName() {
		$person =& Celini::newORDO('Person',$this->get('external_user_id'));
		$clinic =& Celini::newORDO('chlClinic',$person->get('clinic_id'));
		return $clinic->get('full_name');
	}
	
	/**
	 * @access protected
	 */
	function valueList_programs() {
		$db =& new clniDB();
		$tableName = $this->tableName();
		$qExternalUserId = $this->get('external_user_id');
		
		$sql = "
			SELECT 
				p.refprogram_id AS id,
				p.name,
				ru.deleted
			FROM 
				{$tableName} AS ru
				INNER JOIN refprogram AS p USING(refprogram_id) 
			WHERE
				ru.external_user_id = {$qExternalUserId} AND
				ru.deleted = 0";
		$results = $db->execute($sql);
		
		$returnArray = array();
		while ($results && !$results->EOF) {
			if ($results->fields['deleted'] == 1) {
				$results->moveNext();
				continue;
			}
			$returnArray[$results->fields['id']] = $results->fields['name'];
			$results->moveNext();
		}
		return $returnArray;
	}
}
