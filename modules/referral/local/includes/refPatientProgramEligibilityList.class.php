<?php
require_once dirname(__FILE__) . '/refEligibilitySchemaMapper.class.php';

class refPatientProgramEligibilityList
{
	var $_db = null;
	var $_results = null;
	
	function refPatientProgramEligibilityList($patient_id) {
		$me =& Me::getInstance();
		$db =& new clniDB();
		$sql = '
			SELECT 
				IF(pe.refpatient_eligibility_id IS NULL, 0, pe.refpatient_eligibility_id) AS refpatient_eligibility_id,
				prog.refprogram_id,
				prog.name AS program_name,
				prog.schema,
				pe.eligibility,
				pe.federal_poverty_level,
				ev.value as federal_poverty_level_value
			FROM
				refprogram AS prog
				INNER JOIN refuser AS ru USING(refprogram_id)
				LEFT JOIN refpatient_eligibility AS pe ON(
					(prog.refprogram_id = pe.refprogram_id) AND
						(pe.patient_id = ' . (int)$patient_id . ' OR
						 pe.patient_id IS NULL)
					)
				LEFT JOIN (
					SELECT 
						value, `key`
					FROM
						enumeration_value
						JOIN enumeration_definition AS ed USING(enumeration_id)
					WHERE
						ed.name = "federal_poverty_level"
				) AS ev ON(pe.federal_poverty_level = ev.key)
			WHERE
				ru.external_user_id = ' . $db->quote($me->get_person_id()) . ' AND
				ru.deleted = 0
			GROUP BY
				prog.refprogram_id';
		$this->_results = $db->execute($sql);
	}
	
	function nextEligibility() {
		if (!$this->_results || $this->_results->EOF) {
			return false;
		}
		
		$returnArray = array();
		$row = $this->_results->fields;
		$returnArray = $row;
		$returnArray['refPatientEligibility'] =& Celini::newORDO('refPatientEligibility', $row['refpatient_eligibility_id']);
		
		$eligibilitySchema =& new refEligibilitySchemaMapper($row['schema']);
		$eligibilitySchema->inputName = 'refPatientEligibility';
		$returnArray['schema'] = $eligibilitySchema->toInput($returnArray['refPatientEligibility']->get('eligibility'));
		$returnArray['readOnlySchema'] = $eligibilitySchema->toList($returnArray['refPatientEligibility']->get('eligibility'));
		
		$this->_results->moveNext();
		return $returnArray;
	}
}
