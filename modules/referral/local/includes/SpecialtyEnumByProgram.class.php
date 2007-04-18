<?php

class SpecialtyEnumByProgram
{
	var $_array = array();
	var $_programId = null;
	
	function SpecialtyEnumByProgram($refprogram_id) {
		$this->_programId = (int)$refprogram_id;
	}
	
	function toArray() {
		if (count($this->_array) <= 0) {
			$this->_initArray();
		}
		
		return $this->_array;
	}
	
	function _initArray() {
		$db = new clniDB();
		$qProgramId = $db->quote($this->_programId);
		$sql = '
			SELECT
				ev.`key`,
				ev.value
			FROM
				participation_program AS prog
				INNER JOIN refprogram_member AS prog_mem on prog_mem.refprogram_id = participation_program_id
				LEFT JOIN refpractice AS prac ON(prog_mem.external_id = prac.refpractice_id AND prog_mem.external_type = "Practice")
				LEFT JOIN refprovider as prov ON(prog_mem.external_id = prov.refprovider_id AND prog_mem.external_type = "Provider")
				LEFT JOIN refpractice as prov_prac ON(prov.refpractice_id = prov_prac.refpractice_id)
				INNER JOIN refSpecialtyMap AS rsm ON(
					IF(
						rsm.external_type = "refpractice",
						rsm.external_id = prac.refpractice_id,
						rsm.external_id = prov_prac.refpractice_id)
				)
				INNER JOIN enumeration_value AS ev USING(enumeration_value_id)
				INNER JOIN enumeration_definition AS ed ON(ev.enumeration_id = ed.enumeration_id AND ed.name = "refSpecialty")
			WHERE
				prog.participation_program_id = ' . $qProgramId . '
				AND prog.type="referral"
			GROUP BY
				ev.value
			ORDER BY 
				sort';
		$this->_array = $db->getAssoc($sql);
	}
}

?>
