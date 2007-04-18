<?php

$loader->requireOnce('includes/ORDO/ORDOByQueryFinder.class.php');

class refProgram extends ORDataObject {

	var $refprogram_id = '';
	
	var $schema = '';
	
	var $_table = 'refprogram';
	var $_internalName = 'refProgram';
	var $_participationProgram = '';
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id', $id);
			$this->populate();
			$this->_participationProgram = ORDataObject::factory('ParticipationProgram',$this->get('refprogram_id'));
		}
	}
	
	function populate() {
		parent::populate('refprogram_id');
	}
	
	function get_id() {
		return $this->get('refprogram_id');
	}
	
	function set_id($value) {
		$this->set('refprogram_id', $value);
	}

	/**
	 * Use getChildren('memmbers') instead
	 *
	 * @deprecated
	 */
	function &memberArray() {
		$collection =& $this->getChildren('members');
		return $collection;
	}
	
	function &getChildren_members() {
		$returnArray = array();
		$qRefProgramId = $this->dbHelper->quote($this->get('id'));
		$sql = "
			SELECT 
				rpm.*
			FROM
				refprogram_member AS rpm
				LEFT JOIN refpractice AS prac ON(rpm.external_type = 'Practice' AND rpm.external_id = prac.refpractice_id) 
				LEFT JOIN refprovider AS prov ON(rpm.external_type = 'Provider' AND rpm.external_id = prov.refprovider_id)
				LEFT JOIN refpractice AS prov_prac ON(prov_prac.refPractice_id = prov.refpractice_id)
			WHERE
				rpm.refprogram_id = {$qRefProgramId} AND
				rpm.inactive = 0 AND
				IF (prac.status IS NULL, prov_prac.status = 0, prac.status = 0)";
		$finder =& new ORDOByQueryFinder('refProgramMember', $sql);
		$collection =& $finder->find();
		return $collection;
	}
	
	function value_schema() {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('refEligibilitySchema', $this->get('schema'));
	}
	
	
	/**
	 * Returns an associative array of id/name.
	 *
	 * @see     ORDataObject::valueList()
	 * @access  protected
	 * @return  array
	 */
	function genericList() {
		$tableName = $this->tableName();
		$sql = "SELECT refprogram_id, name FROM {$tableName}";
		return $this->dbHelper->getAssoc($sql);
	}
	
	
	/**
	 * Returns an associative array of id/name combos, but only for the programs the currently
	 * logged in user has permissions for.
	 *
	 * @see     genericList(), ORDataObject::valueList()
	 * @access  protected
	 * @return  array
	 */
	function valueList_memberPrograms() {
		$tableName = $this->tableName();
		
		$me =& Me::getInstance();
		$qUserId = $this->dbHelper->quote($me->get_person_id());
		
		$sql = "
			SELECT 
				prog.refprogram_id, 
				name
			FROM
				refprogram AS prog
				INNER JOIN refuser AS ru USING(refprogram_id)
			WHERE
				ru.external_user_id = {$qUserId} AND
				ru.deleted = 0
			GROUP BY
				prog.refprogram_id";
		$return = $this->dbHelper->getAssoc($sql);
		if ($return === false) {
			$return = array();
		}
		return $return;
	}
}
