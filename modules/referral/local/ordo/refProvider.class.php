<?php

$loader->requireOnce('includes/refSpecialtyMapper.class.php');

class refProvider extends ORDataObject
{
	var $refprovider_id = '';
	var $prefix = '';
	var $first_name = '';
	var $middle_name = '';
	var $last_name = '';
	var $direct_line = '';
	var $refpractice_id = '';
	
	var $_specialties = array();
	var $_specialtyKeys = array();
	var $_table = 'refprovider';
	var $_key = 'refprovider_id';
	var $_internalName = 'refProvider';
	
	var $storage_metadata = array(
		'int' => array(), 
		'date' => array(),
		'string' => array(
			'services'     => '',
			'restrictions' => '')
	);

	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',(int) $id);
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate('refprovider_id');
	}
	
	function persist() {
		parent::persist();
		$this->_persistSpecialties();
	}
	
	/**#@+
	 * Accessor/mutator methods
	 *
	 * @access protected
	 */
	function get_id() {
		return $this->get('refprovider_id');
	}
	
	function set_id($value) {
		$this->set('refprovider_id', $value);
	}
	
	function get_specialties() {
		$this->_initSpecialties();
		return $this->_specialties;
	}
	
	function set_specialties($value) {
		$this->_specialtyKeys = (array)$values;
	}
	
	function get_specialty_ids() {
		return array_keys($this->get('specialists'));
	}
	
	/**#@-*/
	
	/**#@+
	 * Virtual accessor
	 *
	 * @access protected
	 */
	 function get_full_name() {
		 return $this->get('prefix') . ' ' . $this->get('first_name') . ' ' . $this->get('last_name');
	 }
	 
	/**#@-*/
	
	
	/**
	 * Return an array of id => name to be used as part of a drop down
	 *
	 * @return array
	 */
	function keyNameArray() {
		$returnArray = array();
		$sql = '
			SELECT 
				refprovider_id AS id,
				' . $this->fullNameSQL() . ' AS name
			FROM 
				' . $this->_table . '
				INNER JOIN refpractice AS prac USING(refpractice_id)
			WHERE
				prac.assign_by = "Provider"';
		$recordSet = $this->_db->Execute($sql);
		while (!$recordSet->EOF) {
			$returnArray[$recordSet->fields['id']] = $recordSet->fields['name'];
			$recordSet->moveNext();
		}
		
		return $returnArray;
	}
	
	
	function &getParent_refPractice() {
		$practice =& Celini::newORDO('refPractice', $this->get('refpractice_id'));
		return $practice;
	}
	
	
	/**
	 * Load all specialities associated with this provider/practice
	 *
	 * @access private
	 */
	function _initSpecialties() {
		$mapper =& new refSpecialtyMapper();
		$this->_specialties = $mapper->find($this->getParent('refPractice'));
	}
	
	/**
	 * @access private
	 * @todo Move this to using {@link refSpecialtyMapper}
	 */
	function _persistSpecialties() {
		if (count($this->_specialtyKeys) <= 0) {
			return;
		}
		
		$sql = 'DELETE FROM refSpecialtyMap WHERE external_type = "refprovider" AND external_id = "' . $this->get('id') . '"';
		$this->_db->Execute($sql);
		
		$valueSQL = array();
		
		$em =& new EnumManager();
		$enumList =& $em->enumList('refSpecialty');
		while ($enumList->valid()) {
			$enumValue =& $enumList->current();
			$enumList->next();
			
			if (!in_array($enumValue->key, $this->_specialtyKeys)) {
				continue;
			}
			$valueSQL[] = '(' .$this->_db->nextId() . ',"refprovider", "' . $this->get('id') . '", "' . $enumValue->enumeration_value_id . '")';
		}
		
		$sql = '
			INSERT INTO refSpecialtyMap 
				(refSpecialityMap_id, external_type, external_id, enumeration_value_id) 
			VALUES ' . implode(', ', $valueSQL);
		$this->_db->Execute($sql);
		// handle mapping of specialist enum to refSpecialtyMap
	}
	
	function &getParent_refProgram() {
		$db =& Celini::dbInstance();
		$sql = "SELECT refprogram_id AS id FROM refprogram_member WHERE external_id = ".$db->quote($this->get('id'));
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			if(!isset($res->fields['id'])) {
				$res->fields['id'] = $res->fields[0];
			}
			$program =& Celini::newORDO('refProgram',$res->fields['id']);
		} else {
			$program =& Celini::newORDO('refProgram');
		}
		return $program;
	}
	
	/**
	 * Returns the SQL required for building a full name out of the database.
	 *
	 * @return string
	 */
	function fullNameSQL() {
		return 'concat(if(prefix != "", concat(prefix, " "), ""), first_name, " ", middle_name, " ", last_name)';
	}
}

