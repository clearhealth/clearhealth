<?php
/**
 * Class the defines the combined family_planning and disease enumeration type
 * Note that we use family's enum_value_id as this value_id and the disease value_id as extra1
 * If this enum is disabled, its status is stored on family->extra2
 */
class EnumType_ConfidentialFamilyPlanningAndDisease extends EnumType_Default {

	/**
	 * Sql table the data is stored in, also the name of the ordo used for updating
	 */
	var $table = 'enumeration_value';

	/**
	 * Name of the ORDO used to update
	 */
	var $ordo = 'EnumerationValue';

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'key' 	=> array('label'=>'Key','size'=>5), 
				'value' => array('label'=>'Value','size'=>15),
				'extra1' => array('type'=>'hidden'),
				'extra2' => false,
				'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
				'status' => array('label'=>'Enabled','type'=>'boolean')
			);
	
	/**
	 * Get an array of enum data
	 *
	 * @param  int $enumerationId
	 * @return array
	 */
	function enumData($enumerationId) {
		$enumerationId = EnforceType::int($enumerationId);
		$ret = array();
		$db =& Celini::dbInstance();
		/* Use this one instead if we only want values that are in BOTH disease and family planning
		$sql = "SELECT fam.enumeration_value_id,fam.enumeration_id,fam.key,fam.value,fam.extra1,fam.extra2 AS `status`
		FROM 
		enumeration_definition AS famdef
		JOIN {$this->table} fam ON fam.enumeration_id = famdef.enumeration_id
		WHERE famdef.name = 'confidential_family_planning_codes'
		AND fam.extra1 > 0 AND fam.extra2 != ''
		ORDER BY fam.sort";
		$res = $db->execute($sql);
		$i = 0;
		while($res && !$res->EOF) {
			$res->fields['sort'] = $i;
			$ret[] = $res->fields;
			$i++;
			$res->moveNext();
		}
		*/
		$sql = "SELECT famdef.enumeration_id famid,disdef.enumeration_id disid
		FROM enumeration_definition famdef,enumeration_definition disdef
		WHERE famdef.name='confidential_family_planning_codes'
		AND disdef.name='confidential_disease_codes'";
		$idres = $db->execute($sql);
		$i = 1;
		foreach($idres->fields as $enumid) {
			$sql = "SELECT * FROM
			{$this->table} WHERE enumeration_id = $enumid ORDER BY sort";
			$res = $db->execute($sql);
			while($res && !$res->EOF) {
				$res->fields['sort'] = $i;
				$res->fields['key'] = $i;
				$ret[] = $res->fields;
				$i++;
				$res->moveNext();
			}
		}
		return $ret;
	}

	/**
	 * Update an enum value with an array of data
	 * This will update both the family planning AND disease code enums
	 *
	 * @param	array	$data
	 */
	function update($data) {
		/* this code is if we're using only values that occur in BOTH fam and disease.
		   otherwise, you have to update those specific enums as update from here does nothing.
		$id = 0;
		$db =& Celini::dbInstance();
		if (isset($data['enumeration_value_id'])) {
			$id = $data['enumeration_value_id'];
		}
		if($id == 0) {
			unset($data['enumeration_value_id']);
			// Check to see if it exists in fam
			$sql = "SELECT fam.*
			FROM 
			enumeration_definition AS famdef
			JOIN {$this->table} fam ON fam.enumeration_id = famdef.enumeration_id
			WHERE fam.value = ".$db->quote($data['value'])." AND
			famdef.name = 'confidential_family_planning_codes'
			ORDER BY fam.sort";
			$res = $db->execute($sql);
			if($res->EOF) {
				$sql = "SELECT fam.enumeration_id,count(fam.enumeration_id) eids 
				FROM {$this->table} AS fam
				JOIN enumeration_definition AS famdef ON famdef.name='confidential_family_planning_codes'
				WHERE fam.enumeration_id = famdef.enumeration_id
				GROUP BY fam.enumeration_id";
				$res = $db->execute($sql);
				$data['enumeration_id'] = $res->fields['enumeration_id'];
				$data['sort'] = $res->fields['eids'];
				$data['extra2'] = $data['status'];
				unset($data['status']);
				$famev =& Celini::newORDO($this->ordo);
				$famev->populate_array($data);
				$famev->persist();
			} else {
				$famev = & Celini::newORDO($this->ordo,$res->fields['enumeration_value_id']);
				$famev->populate_array($data); // We're using the fam enum_value_id, so we don't have to worry about changing it
			}
			// Check to see if it exists in dis
			$sql = "SELECT dis.*
			FROM 
			enumeration_definition AS disdef
			JOIN {$this->table} AS dis ON dis.enumeration_id = disdef.enumeration_id
			WHERE dis.value = ".$db->quote($data['value'])." AND
			disdef.name = 'confidential_disease_codes'";
			$res = $db->execute($sql);
			if($res->EOF) {
				$sql = "SELECT dis.enumeration_id,count(dis.enumeration_id) eids 
				FROM {$this->table} AS dis
				JOIN enumeration_definition AS disdef ON disdef.name='confidential_disease_codes'
				WHERE dis.enumeration_id = disdef.enumeration_id
				GROUP BY dis.enumeration_id";
				$res = $db->execute($sql);
				$data['enumeration_id'] = $res->fields['enumeration_id'];
				$data['sort'] = $res->fields['eids'];
				$disev =& Celini::newORDO($this->ordo);
				$disev->populate_array($data);
				$disev->persist();
			} else {
				$disev = & Celini::newORDO($this->ordo,$res->fields['enumeration_value_id']);
				$data['enumeration_value_id'] = $res->fields['enumeration_value_id'];
				$disev->populate_array($data);
				$disev->persist();
			}
			$famev->set('extra1',$disev->get('enumeration_value_id'));
			$famev->persist();
		} else {
			// This will change the values for both the family planning and disease enums
			// If you don't want to have them used, just disable that pair.
			$disev =& Celini::newORDO($this->ordo,$data['extra1']);
			$data['extra2'] = $data['status'];
			unset($data['enumeration_id']);
			unset($data['status']);
			unset($data['extra1']); // We don't want to use this since it's just the disease value_id
			$ev =& Celini::newORDO($this->ordo,$data['enumeration_value_id']);
			$ev->populate_array($data);
			$ev->persist();
			unset($data['enumeration_value_id']);
			$disev->populate_array($data);
			$disev->persist();
		}
		*/
	}

}
?>