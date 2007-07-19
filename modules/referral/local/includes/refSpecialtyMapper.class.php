<?php

/**
 * Handles mapping a relationship between a refSpecialty enum value and an 
 * external ORDO object
 */
class refSpecialtyMapper
{
	var $_db = null;
	
	function refSpecialtyMapper() {
		$this->_db =& new clniDB();
	}
	
	
	/**
	 * Find all specialties that have been mapped to a given ORDO
	 *
	 * @param  ORDataObject
	 * @return array
	 */
	function find(&$ordo) {
		$returnArray = array();
		$sql = sprintf('
			SELECT 
				e.key, e.value 
			FROM 
				enumeration_value AS e
				JOIN refSpecialtyMap AS sm USING(enumeration_value_id)
			WHERE
				sm.external_type = "%s" AND
				sm.external_id = "%d"',
			strtolower(get_class($ordo)),
			$ordo->get('id')
		);

		if (($recordSet = $this->_db->Execute($sql)) === false) {
			return $returnArray;
		}
		
		while (!$recordSet->EOF) {
			$returnArray[$recordSet->fields['key']] = $recordSet->fields['value'];
			$recordSet->MoveNext();
		}
		
		return $returnArray;
	}
	
	
	/**
	 * Saves an array of specialties for a given ORDO
	 *
	 * @param  ORDataObject
	 *    The ORDO to map to
	 * @param  array
	 *    An array of enum keys to use for mapping
	 */
	function persist(&$ordo, $array) {
		$ordoName = strtolower(get_class($ordo));
		
		$sql = sprintf(
			'DELETE FROM refSpecialtyMap WHERE external_type = "%s" AND external_id = "%d"', 
			$ordoName, $ordo->get('id'));
		$this->_db->Execute($sql);
		
		$valueSQL = array();
		
		$em =& new EnumManager();
		$enumList =& $em->enumList('refSpecialty');
		while ($enumList->valid()) {
			$enumValue =& $enumList->current();
			$enumList->next();
			if (!in_array($enumValue->key, $array)) {
				continue;
			}
			$valueSQL[] = sprintf('"%s", "%d", "%d"',
				$ordoName,
				$ordo->get('id'),
				$enumValue->enumeration_value_id);
		}
		
		$sql = '
			INSERT INTO refSpecialtyMap 
				(refSpecialityMap_id,external_type, external_id, enumeration_value_id) 
			VALUES (' . $this->_db->nextId() . ',' . implode(', ', $valueSQL)  . ")";
		$this->_db->Execute($sql);
	}
}
