<?php
/*****************************************************************************
*       NQF0024.php
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


class NQF0024 extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0024: gov.cms.nqf.0024 (Alt Core - 2)
	 * Title: Weight Assessment and Counseling for Children and Adolescents
	 * Description: Percentage of patients 2 -17 years of age who had an outpatient visit with a Primary Care Physician (PCP) or OB/GYN and who had evidence of BMI percentile documentation, counseling for nutrition and counseling for physical activity during the measurement year.
	 * Jay's comments: Is bmi present or patient education material provided (visit detail tab) for nutrition or patient education materials provided for physical activity. Add education entries for "Adolescent Nutrition" "Pediatric Nutrition" "Adolescent Physical Activity" "Pediatric Physical Activity"
	 */
	public function populate() {
		$dateStart = $this->dateStart.' 00:00:00';
		$dateEnd = $this->dateEnd.' 23:59:59';
		$providerId = (int)$this->providerId;
		$db = Zend_Registry::get('dbAdapter');
		$pregnancyICD9Codes = array(
			'V24', 'V24.0', 'V24.2', 'V25', 'V25.01', 'V25.02', 'V25.03',
			'V25.09', 'V26.81', 'V28', 'V28.3', 'V28.81', 'V28.82', 'V72.4',
			'V72.40', 'V72.41', 'V72.42',
		);
		$diagCodeList = $this->_formatCodeList($pregnancyICD9Codes);

		$ret = array();
		$criteria = array();
		$criteria['Criteria 1'] = 'BETWEEN 2 AND 17'; // Population criteria 1
		$criteria['Criteria 2'] = 'BETWEEN 2 AND 11'; // Population criteria 2
		$criteria['Criteria 3'] = 'BETWEEN 11 AND 17'; // Population criteria 3
		foreach ($criteria as $key=>$value) {
			/*$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) {$value}) AND
				(encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}')";*/
			$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) {$value})";
			$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";

			$lookupTables = array(
				array(
					'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
					'where'=>"problemLists.code NOT IN (".implode(',',$diagCodeList['code']).")",
				),
				array(
					'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
					'where'=>"patientDiagnosis.code NOT IN (".implode(',',$diagCodeList['code']).")",
				),
				array(
					'join'=>'LEFT JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
						INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
					'where'=>"(genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$diagCodeList['generic'])."))",
				),
			);
			$denominator = array();
			foreach ($lookupTables as $lookup) {
				$sql = "SELECT patient.person_id AS patientId,
					patient.record_number AS MRN
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				INNER JOIN encounter ON encounter.patient_id = patient.person_id
				{$lookup['join']}
				WHERE {$initialPopulation} AND {$lookup['where']}";
				//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
				$dbStmt = $db->query($sql);
				while ($row = $dbStmt->fetch()) {
					$denominator[$row['patientId']] = $row;
				}
			}

			// Numerator 1
			$numerator1 = array();
			foreach ($lookupTables as $lookup) {
				$sql = "SELECT patient.person_id AS patientId,
					patient.record_number AS MRN
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				INNER JOIN encounter ON encounter.patient_id = patient.person_id
				INNER JOIN vitalSignGroups ON vitalSignGroups.personId = patient.person_id
				INNER JOIN vitalSignValues ON vitalSignValues.vitalSignGroupId = vitalSignGroups.vitalSignGroupId
				{$lookup['join']}
				WHERE {$initialPopulation} AND {$lookup['where']} AND
					(vitalSignValues.vital = 'BMI' AND vitalSignValues.value != '' AND vitalSignValues.value IS NOT NULL)";
				//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
				$dbStmt = $db->query($sql);
				while ($row = $dbStmt->fetch()) {
					$numerator1[$row['patientId']] = $row;
				}
			}

			// Numerator 2
			$numerator2 = array();
			foreach ($lookupTables as $lookup) {
				$sql = "SELECT patient.person_id AS patientId,
					patient.record_number AS MRN
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				INNER JOIN encounter ON encounter.patient_id = patient.person_id
				INNER JOIN patientEducations ON patientEducations.patientId = patient.person_id
				{$lookup['join']}
				WHERE {$initialPopulation} AND {$lookup['where']} AND
					patientEducations.code IN ('AN', 'PN') AND
					(patientEducations.dateTime BETWEEN '{$dateStart}' AND '{$dateEnd}')";
				//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
				$dbStmt = $db->query($sql);
				while ($row = $dbStmt->fetch()) {
					$numerator2[$row['patientId']] = $row;
				}
			}

			// Numerator 3
			$numerator3 = array();
			foreach ($lookupTables as $lookup) {
				$sql = "SELECT patient.person_id AS patientId,
					patient.record_number AS MRN
				FROM patient
				INNER JOIN person ON person.person_id = patient.person_id
				INNER JOIN encounter ON encounter.patient_id = patient.person_id
				INNER JOIN patientEducations ON patientEducations.patientId = patient.person_id
				{$lookup['join']}
				WHERE {$initialPopulation} AND {$lookup['where']} AND
					patientEducations.code IN ('APA', 'PPA') AND
					(patientEducations.dateTime BETWEEN '{$dateStart}' AND '{$dateEnd}')";
				//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
				$dbStmt = $db->query($sql);
				while ($row = $dbStmt->fetch()) {
					$numerator3[$row['patientId']] = $row;
				}
			}

			$nctr1 = count($numerator1);
			$nctr2 = count($numerator2);
			$nctr3 = count($numerator3);
			$dctr = count($denominator);
			$percentage1 = self::calculatePerformanceMeasure($dctr,$nctr1);
			$percentage2 = self::calculatePerformanceMeasure($dctr,$nctr2);
			$percentage3 = self::calculatePerformanceMeasure($dctr,$nctr3);
			self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr1,'percentage'=>$percentage1);
			self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr2,'percentage'=>$percentage2);
			self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr3,'percentage'=>$percentage3);
			$ret[] = $key.' = D: '.$dctr.'; N1: '.$nctr1.'; P1: '.$percentage1.'; N2: '.$nctr2.'; P2: '.$percentage2.' N3: '.$nctr3.'; P3: '.$percentage3;
		}
		return implode("<br/>\n",$ret);
	}

}
