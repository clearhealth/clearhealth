<?php
/*****************************************************************************
*       MeasureCalculation.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class MeasureCalculation implements NSDRMethods {

	protected $tthis;
	protected $data;
	protected $dateStart;
	protected $dateEnd;
	protected $providerId = 0;

	public function nsdrMostRecent($tthis,$context,$data) {
	}

	function nsdrPersist($tthis,$context,$data) {
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$this->tthis = $tthis;
		$this->providerId = (int)$context;
		$this->data = $data;

		$this->dateStart = date('Y-m-d 00:00:00');
		$this->dateEnd = date('Y-m-d 23:59:59');
		if (isset($tthis->_attributes['dateStart'])) $this->dateStart = date('Y-m-d 00:00:00',strtotime($tthis->_attributes['dateStart']));
		if (isset($tthis->_attributes['dateEnd'])) $this->dateEnd = date('Y-m-d 23:59:59',strtotime($tthis->_attributes['dateEnd']));

		$prefix = 'gov.hhs.stage1.measure.';
		$id = substr($tthis->_nsdrNamespace,strlen($prefix));
		$method = 'populateMeasure'.$id;
		if (!method_exists($this,$method)) return 'ID: '.$id;
		return $this->$method();
	}

	protected function countVisits($params = array()) {
		$db = Zend_Registry::get('dbAdapter');
		$join = isset($params['join'])?$params['join']:'';
		$where = isset($params['where'])?"AND ({$params['where']})":'';
		$sql = "SELECT COUNT(DISTINCT encounter.patient_id) AS ctr
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			{$join}
			WHERE (encounter.treating_person_id = {$this->providerId}) AND
				(encounter.date_of_treatment BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}')
				{$where}";
		trigger_error($sql);
		$ctr = 0;
		if ($row = $db->fetchRow($sql)) $ctr = $row['ctr'];
		return $ctr;
	}

	public function populateMeasure1() {
		// Objective: Maintain an up-to-date problem list of current and active diagnoses
		// Measure: More than 80% of all unique patients seen by the EP have at least one entry or an indication that no problems are known for the patient recorded as structured data
		$tables = array(
			'join'=>"INNER JOIN problemLists ON problemLists.personId = encounter.patient_id",
			'where'=>"(problemLists.status = 'Active') AND
				(((problemLists.dateOfOnset BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(problemLists.providerId = encounter.treating_person_id)) OR
				(problemLists.codeTextShort LIKE '%no problem%'))",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 80%';
	}

	public function populateMeasure2() {
		// Objective: Maintain active medication list
		// Measure: More than 80% of all unique patients seen by the EP have at least one entry (or an indication that the patient is not currently prescribed any medication) recorded as structured data
		$tables = array(
			'join'=>"INNER JOIN medications ON medications.personId = encounter.patient_id",
			'where'=>"(medications.dateDiscontinued = '0000-00-00 00:00:00') AND
				(((medications.datePrescribed BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(medications.prescriberPersonId = encounter.treating_person_id)) OR
				(medications.description LIKE '%no medication prescribed%'))"
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 80%';
	}

	public function populateMeasure3() {
		// Objective: Maintain active medication allergy list
		// Measure: More than 80% of all unique patients seen by the EP have at least one entry (or an indication that the patient has no known medication allergies) recorded as structured data
		$tables = array(
			'join'=>"INNER JOIN patientAllergies ON patientAllergies.patientId = encounter.patient_id
				INNER JOIN audits ON audits.userId = encounter.treating_person_id",
			'where'=>"((patientAllergies.active = 1) AND
				(patientAllergies.dateTimeReaction BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(audits.objectId = patientAllergies.patientAllergyId AND audits.objectClass = 'PatientAllergy')) OR
				(patientAllergies.noKnownAllergies = 1)"
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 80%';
	}

	public function populateMeasure4() {
		// Objective: Record demographics o Preferred language o Gender o Race o Ethnicity o Date of Birth
		// Measure: More than 50% of all unique patients seen by the EP have demographics recorded as structured data
		$tables = array(
			'join'=>"INNER JOIN patientStatistics ON patientStatistics.personId = encounter.patient_id",
			'where'=>"(patientStatistics.language != '') AND
				(person.gender != '') AND
				(patientStatistics.Race != '') AND
				(patientStatistics.ethnicity != '') AND
				(person.date_of_birth != '0000-00-00')",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

	public function populateMeasure5() {
		// Objective: Use certified EHR technology to identify patient-specific education resources and provide those resources to the patient if appropriate
		// Measure: More than 10% of all unique patients seen by the EP during the EHR reporting period are provided patient-specific education resources
		$tables = array(
			'join'=>"INNER JOIN patientEducations ON patientEducations.patientId = encounter.patient_id
				INNER JOIN audits ON audits.userId = encounter.treating_person_id",
			'where'=>"(patientEducations.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(audits.objectId = CONCAT(patientEducations.code,';',patientEducations.patientId) AND audits.objectClass = 'PatientEducation') AND
				(patientEducations.code IN ('CYBS','LTMA'))",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 10%';
	}

	public function populateMeasure6() {
		// Objective: Provide patients with timely electronic access to their health information (including lab results, problem list, medication lists, medication allergies) within four business days of the information being available to the EP
		// Measure: More than 10% of all unique patients seen by the EP are provided timely (available to the patient within four business days of being updated in the certified EHR technology) electronic access to their health information subject to the EP’s discretion to withhold certain information
		$tables = array(
			'join'=>"INNER JOIN number ON number.person_id = encounter.patient_id",
			'where'=>"(number.type = 'PORTAL' OR number.type = 'PORTALTXT')",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 10%';
	}

	public function populateMeasure7() {
		// Objective: Use certified EHR technology to identify patient-specific education resources and provide those resources to the patient if appropriate
		// Measure: More than 10% of all unique patients seen by the EP are provided patient-specific education resources
		$tables = array(
			'join'=>"INNER JOIN patientEducations ON patientEducations.patientId = encounter.patient_id
				INNER JOIN audits ON audits.userId = encounter.treating_person_id",
			'where'=>"(audits.objectId = CONCAT(patientEducations.code,';',patientEducations.patientId) AND audits.objectClass = 'PatientEducation') OR
				(patientEducations.code IN ('CYBS','LTMA'))",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits();
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 10%';
	}

	public function populateMeasure8() {
		// Objective: Use CPOE for medication orders directly entered by any licensed healthcare professional who can enter orders into the medical record per state, local and professional guidelines
		// Measure: More than 30% of unique patients with at least one medication in their medication list seen by the EP have at least one medication order entered using CPOE
		$tables = array(
			'join'=>"INNER JOIN medications ON medications.personId = encounter.patient_id",
			'where'=>"(medications.datePrescribed BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(medications.prescriberPersonId = encounter.treating_person_id)",
		);
		$denominator = $this->countVisits($tables);
		$tables['where'] .= " AND (medications.transmit = 'ePrescribe')";
		$numerator = $this->countVisits($tables);
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 30%';
	}

	public function populateMeasure9() {
		// Objective: Generate and transmit permissible prescriptions electronically (eRx)
		// Measure: More than 40% of all permissible prescriptions written by the EP are transmitted electronically using certified EHR technology
		$db = Zend_Registry::get('dbAdapter');
		$sql = "SELECT COUNT(DISTINCT medications.personId) AS ctr
			FROM medications
			WHERE (medications.datePrescribed BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(medications.prescriberPersonId = {$this->providerId})";
		$denominator = 0;
		if ($row = $db->fetchRow($sql)) $denominator = $row['ctr'];
		$sql .= " AND (medications.transmit = 'ePrescribe')";
		$numerator = 0;
		if ($row = $db->fetchRow($sql)) $numerator = $row['ctr'];
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 40%';
	}

	public function populateMeasure10() {
		// Objective: Record and chart changes in vital signs: o Height o Weight o Blood pressure o Calculate and display BMI o Plot and display growth charts for children 2-20 years, including BMI
		// Measure: More than 50% of all unique patients age 2 and over seen by the EP, height, weight and blood pressure are recorded as structured data
		$where = "((DATE_FORMAT('{$this->dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$this->dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 2)";
		$sql = "SELECT vitalSignValues.vital AS vital, vitalSignGroups.vitalSignGroupId AS vitalSignGroupId, encounter.patient_id AS patientId
			FROM vitalSignValues
			INNER JOIN vitalSignGroups ON vitalSignGroups.vitalSignGroupId = vitalSignValues.vitalSignGroupId
			INNER JOIN encounter ON encounter.patient_id = vitalSignGroups.personId
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			WHERE ({$where}) AND
				(encounter.treating_person_id = {$this->providerId}) AND
				(encounter.date_of_treatment BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(vitalSignGroups.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				((vitalSignValues.vital = 'height' AND vitalSignValues.value != '') OR
				 (vitalSignValues.vital = 'weight' AND vitalSignValues.value != '') OR
				 (vitalSignValues.vital = 'bloodPressure' AND vitalSignValues.value != '')) AND
				(vitalSignGroups.enteringUserId = encounter.treating_person_id)";
		$data = array();
		$db = Zend_Registry::get('dbAdapter');
		$stmt = $db->query($sql);
		while ($row = $stmt->fetch()) {
			$data[$row['patientId']][$row['vitalSignGroupId']][$row['vital']] = $row['vital'];
		}
		$numerator = 0;
		foreach ($data as $patientId=>$vitalSignGroups) {
			foreach ($vitalSignGroups as $vitalSignGroupId=>$vitalSignGroup) {
				if (isset($vitalSignGroup['height']) &&
				    isset($vitalSignGroup['weight']) &&
				    isset($vitalSignGroup['bloodPressure'])) {
					$numerator++;
					break;
				}
			}
		}
		//file_put_contents('/tmp/vitals.txt',print_r($data,true));
		//trigger_error($sql);
		$denominator = $this->countVisits(array('where'=>$where));
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

	public function populateMeasure11() {
		// Objective: Record smoking status for patients 13 years old or older
		// Measure: More than 50% of all unique patients 13 years old or older seen by the EP have smoking status recorded as structured data
		$where = "((DATE_FORMAT('{$this->dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$this->dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 13)";
		$tables = array(
			'join'=>"INNER JOIN clinicalNotes ON clinicalNotes.personId = encounter.patient_id
				INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId",
			'where'=>"({$where}) AND 
				(genericData.objectClass = 'ClinicalNote') AND
				(genericData.name = 'com.clearhealth.smokingStatus') AND
				(clinicalNotes.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(clinicalNotes.authoringPersonId = encounter.treating_person_id)",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits(array('where'=>$where));
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

	public function populateMeasure12() {
		// Objective: Incorporate clinical lab-test results into certified EHR technology as structured data
		// Measure: More than 40% of all clinical lab tests results ordered by the EP during the EHR reporting period whose results are either in a positive/negative or numerical format are incorporated in certified EHR technology as structured data
		$db = Zend_Registry::get('dbAdapter');
		$sql = "SELECT COUNT(DISTINCT lab_order.patient_id) AS ctr
			FROM lab_result
			INNER JOIN lab_test ON lab_test.lab_test_id = lab_result.lab_test_id
			INNER JOIN orders ON orders.orderId = lab_test.lab_order_id
			INNER JOIN lab_order ON lab_order.lab_order_id = orders.orderId
			WHERE (lab_result.observation_time BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(orders.providerId = {$this->providerId})";
		$denominator = 0;
		if ($row = $db->fetchRow($sql)) $denominator = $row['ctr'];
		$sql .= " AND (lab_result.value REGEXP '^(-|\\\+)?([0-9]+\\\.[0-9]*|[0-9]*\\\.[0-9]+|[0-9]+)$')";
		$numerator = 0;
		if ($row = $db->fetchRow($sql)) $numerator = $row['ctr'];
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 40%';
	}

	public function populateMeasure13() {
		// Objective: Provide patients with an electronic copy of their health information (including diagnostic test results, problem list, medication lists, medication allergies), upon request
		// Measure: More than 50% of all patients of the EP who request an electronic copy of their health information are provided it within 3 business days
		$tables = array(
			'join'=>"INNER JOIN audits ON audits.userId = encounter.treating_person_id",
			'where'=>"(audits.objectClass = 'GenericAccessAudit') AND
				(audits.objectId = CONCAT(encounter.patient_id,';0')) AND
				(audits.type = ".GenericAccessAudit::CCD_ALL_XML.") AND
				(audits.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}')",
		);
		$denominator = $this->countVisits($tables);
		$tables['where'] .= " AND (ABS(DATEDIFF(DATE_FORMAT(encounter.date_of_treatment,'%Y-%m-%d'),DATE_FORMAT(audits.dateTime,'%Y-%m-%d'))) <= 5)";
		$numerator = $this->countVisits($tables);
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

	public function populateMeasure14() {
		// Objective: Provide clinical summaries for patients for each office visit
		// Measure: Clinical summaries provided to patients for more than 50% of all office visits within 3 business days
		$tables = array(
			'join'=>"INNER JOIN audits ON audits.userId = encounter.treating_person_id",
			'where'=>"(audits.objectClass = 'GenericAccessAudit') AND
				(audits.objectId = CONCAT(encounter.patient_id,';',encounter.encounter_id)) AND
				(audits.type = ".GenericAccessAudit::CCD_VISIT_XML.") AND
				(audits.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}')",
		);
		$denominator = $this->countVisits($tables);
		$tables['where'] .= " AND (ABS(DATEDIFF(DATE_FORMAT(encounter.date_of_treatment,'%Y-%m-%d'),DATE_FORMAT(audits.dateTime,'%Y-%m-%d'))) <= 5)";
		$numerator = $this->countVisits($tables);
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

	public function populateMeasure15() {
		// Objective: Send reminders to patients per patient preference for preventive/ follow up care
		// Measure: More than 20% of all unique patients 65 years or older or 5 years old or younger were sent an appropriate reminder during the EHR reporting period
		$where = "((DATE_FORMAT('{$this->dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$this->dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 65) OR ((DATE_FORMAT('{$this->dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$this->dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) <= 5)";
		$tables = array(
			'join'=>"INNER JOIN patient_note ON patient_note.patient_id = encounter.patient_id",
			'where'=>"({$where}) AND
				(patient_note.posting = 0) AND
				(patient_note.reason = 'REMINDER') AND
				(patient_note.user_id = encounter.treating_person_id) AND
				(patient_note.note_date BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}')",
		);
		$numerator = $this->countVisits($tables);
		$denominator = $this->countVisits(array('where'=>$where));
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 20%';
	}

	public function populateMeasure16() {
		// Objective: The EP who receives a patient from another setting of care or provider of care or believes an encounter is relevant should perform medication reconciliation
		// Measure: The EP performs medication reconciliation for more than 50% of transitions of care in which the patient is transitioned into the care of the EP.
		$db = Zend_Registry::get('dbAdapter');
		$sql = "SELECT COUNT(DISTINCT medications.personId) AS ctr
			FROM medications
			WHERE (medications.patientReported = 1) AND
				(medications.datePrescribed BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(medications.prescriberPersonId = {$this->providerId})";
		$denominator = 0;
		if ($row = $db->fetchRow($sql)) $denominator = $row['ctr'];
		$sql = "SELECT COUNT(DISTINCT medications.personId) AS ctr
			FROM medications
			INNER JOIN clinicalNotes ON clinicalNotes.personId = medications.personId
			INNER JOIN clinicalNoteDefinitions ON clinicalNoteDefinitions.clinicalNoteDefinitionId = clinicalNotes.clinicalNoteDefinitionId
			WHERE (medications.patientReported = 1) AND
				(medications.datePrescribed BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(medications.prescriberPersonId = {$this->providerId}) AND
				(clinicalNoteDefinitions.title = 'Referral') AND
				(clinicalNotes.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}')";
		$numerator = 0;
		if ($row = $db->fetchRow($sql)) $numerator = $row['ctr'];
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

	public function populateMeasure17() {
		// Objective: The EP who transitions their patient to another setting of care or provider of care or refers their patient to another provider of care should provide summary of care record for each transition of care or referral
		// Measure: The EP who transitions or refers their patient to another setting of care or provider of care provides a summary of care record for more than 50% of transitions of care and referrals
		$db = Zend_Registry::get('dbAdapter');
		$sql = array(
			'select'=>"SELECT COUNT(DISTINCT clinicalNotes.personId) AS ctr
				FROM clinicalNotes",
			'join'=>"INNER JOIN clinicalNoteDefinitions ON clinicalNoteDefinitions.clinicalNoteDefinitionId = clinicalNotes.clinicalNoteDefinitionId",
			'where'=>"WHERE (clinicalNotes.authoringPersonId = {$this->providerId}) AND
					(clinicalNoteDefinitions.title = 'Referral') AND
					(clinicalNotes.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}')"
		);
		$denominator = 0;
		if ($row = $db->fetchRow(implode("\n",$sql))) $denominator = $row['ctr'];
		// check for cases where referral order exists and then that CCD download or print (not visit) was on same day
		$sql['join'] .= " INNER JOIN audits ON audits.userId = clinicalNotes.authoringPersonId";
		$sql['where'] .= " AND (audits.objectClass = 'GenericAccessAudit') AND
				(audits.objectId = CONCAT(clinicalNotes.personId,';',clinicalNotes.visitId)) AND
				(audits.type = ".GenericAccessAudit::CCD_ALL_XML." OR audits.type = ".GenericAccessAudit::CCD_ALL_PRINT.") AND
				(audits.dateTime BETWEEN '{$this->dateStart}' AND '{$this->dateEnd}') AND
				(audits.userId = clinicalNotes.authoringPersonId) AND
				(DATE_FORMAT(audits.dateTime,'%Y-%m-%d') = DATE_FORMAT(clinicalNotes.dateTime,'%Y-%m-%d'))";
		$numerator = 0;
		if ($row = $db->fetchRow(implode("\n",$sql))) $numerator = $row['ctr'];
		$percentage = 0;
		if ($denominator > 0) $percentage = round($numerator/$denominator * 100);
		return 'N: '.$numerator.'; D: '.$denominator.'; P: '.$percentage.'% > 50%';
	}

}
