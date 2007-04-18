<?php

require_once CELINI_ROOT . '/ordo/ORDataObject.class.php';

class refProgramMember extends ORDataObject
{
	var $refprogram_member_id = '';
	
	var $refprogram_id = '';
	var $external_type = '';
	var $external_id   = '';
	var $inactive = 0;
	
	var $_table = 'refprogram_member';
	var $_internalName = 'refProgramMember';
	
	var $_name = '';
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id', $id);
			$this->populate();
		}
	}
	
	function setupActiveByProgramAndExternalTypeId($refprogram_id, $external_type, $external_id) {
		$this->set('refprogram_id', $refprogram_id);
		$this->set('external_type', $external_type);
		$this->set('external_id', $external_id);
		
		$qRefProgramId = $this->dbHelper->quote($this->get('refprogram_id'));
		$qExternalType = $this->dbHelper->quote($this->get('external_type'));
		$qExternalId = $this->dbHelper->quote($this->get('external_id'));
		$tableName = $this->tableName();
		
		$sql = "
			SELECT 
				*
			FROM
				{$tableName} 
			WHERE
				inactive = 0 AND
				refprogram_id = {$qRefProgramId} AND
				external_type = {$qExternalType} AND 
				external_id = {$qExternalId}";
		$this->helper->populateFromQuery($this, $sql);
	}
	
	function populate() {
		parent::populate('refprogram_member_id');
	}
	
	function get_id() {
		return $this->get('refprogram_member_id');
	}
	
	function set_id($value) {
		$this->set('refprogram_member_id', $value);
	}
	
	
	/**
	 * Use value('name')
	 *
	 * @deprecated
	 */
	function get_name() {
		return $this->value('name');
	}
	
	/**#@+
	 * Virtual accessor
	 */
	function value_name() {
		if (empty($this->_name)) {
			$provider =& Celini::newORDO('refProvider');
			$sql = sprintf(
				'SELECT %s AS name FROM refpractice AS prac %s WHERE %s = ' . $this->get('external_id'),
				$this->get('external_type') == 'Provider' ? '' . $provider->fullNameSQL() : 'prac.name',
				$this->get('external_type') == 'Provider' ? 'INNER JOIN refprovider AS prov USING(refpractice_id)' : '',
				$this->get('external_type') == 'Provider' ? 'prov.refprovider_id' : 'prac.refpractice_id');
			$this->_name = $this->_db->GetOne($sql);
		}
		return $this->_name;
	}
	
	function value_name_link() {
		$provider =& Celini::newORDO('refProvider');
		$idColumn = $this->get('external_type') == 'Provider' ? 'prov.refprovider_id' : 'prac.refpractice_id'; 
		$nameColumn = $this->get('external_type') == 'Provider' ? '' . $provider->fullNameSQL() : 'prac.name';
		$joinSql = $this->get('external_type') == 'Provider' ? 'INNER JOIN refprovider AS prov USING(refpractice_id)' : '';
		$qExternalId = $this->dbHelper->quote($this->get('external_id'));
		$sql = "
			SELECT 
				prac.refpractice_id AS id,
				{$nameColumn} AS name 
			FROM
				refpractice AS prac {$joinSql}
			WHERE 
				{$idColumn} = {$qExternalId}";
		$result =& $this->dbHelper->execute($sql);
		$link = Celini::link('edit/' . $result->fields['id'], 'refpractice', 'main') . 'program_id=' . $this->get('refprogram_id');
		return "<a href='{$link}'>{$result->fields[name]}</a>";
		
	}
	
	function get_specialties() {
		$ordoName = 'ref' . $this->get('external_type');
		$ordoObject =& Celini::newORDO($ordoName, $this->get('external_id'));
		return $ordoObject->get('specialties');
	}
	
	/**#@-*/
	
	
	/**
	 * Returns an array of refProgramMemberSlot objects
	 *
	 * This needs some work...  There's no need to do 12 queries here I imagine.  
	 */
	function &getSlots() {
		$returnArray = array();
		for ($i = 0; $i < 12; $i++) {
			$month = date('m', strtotime($i . ' month'));
			$year = date('Y', strtotime($i . ' month'));
			$sql = sprintf(
				'SELECT * FROM refprogram_member_slot WHERE year = "%d" AND month = "%d" AND refprogram_member_id = "%d" LIMIT 1',
				$year, $month, $this->get('id')
			);
			$row = $this->_db->GetRow($sql);
			$newSlot =& Celini::newORDO('refProgramMemberSlot');
			$newSlot->populate_array($row);
			if (count($row) == 0) {
				$newSlot->set('external_id', $this->get('external_id'));
				$newSlot->set('external_type', $this->get('external_type'));
				$newSlot->set('month', $month);
				$newSlot->set('year', $year);
				$newSlot->set('refprogram_member_id', $this->get('id'));
			}
			$returnArray[] =& $newSlot;
		}
		return $returnArray;
	}
}
