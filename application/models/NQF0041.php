<?php
/*****************************************************************************
*       NQF0041.php
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


class NQF0041 extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0041 / PQRI110: gov.cms.nqf.0041 (Alt Core - 1)
	 * Title: Preventive Care and Screening: Influenza Immunization for Patients >= 50 Years Old
	 * Description: Percentage of patients aged 50 years and older who received an influenza immunization during the flu season (September through February).
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$providerId = (int)$this->providerId;

		$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 50)";
		$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";
		// initial population AND >= 2 office visit

		// denominator = initial patient population + denominator
		$initialPopulation .= " AND
				patientImmunizations.immunization LIKE 'influenza%' AND
				/* (DAYOFYEAR(patientImmunizations.dateAdministered) BETWEEN 58 AND 122) */
				(DATE_FORMAT(patientImmunizations.dateAdministered,'%m')+0 <= 2 OR DATE_FORMAT(patientImmunizations.dateAdministered,'%m')+0 >= 9)";
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN patientImmunizations ON patientImmunizations.patientId = patient.person_id
			WHERE {$initialPopulation}
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$denominator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$denominator[$row['patientId']] = $row;
		}

		// RxNorm: 857924 , 857942 , 857965
		// NDC equivalent: 33332-010-01; 33332-110-10; 49281-010-10; 49281-010-25; 49281-010-50; 49281-386-15; 49281-387-65
		$ndcCodes = array(
			// TODO: check for correct NDC
			'3333201001', '3333211010', '4928101010', '4928101025', '4928101050', '4928138615', '4928138765',
		);
		$ndc = $this->_formatCodeList($ndcCodes);
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN medications ON medications.personId = patient.person_id
			INNER JOIN patientImmunizations ON patientImmunizations.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				medications.hipaaNDC IN (".implode(',',$ndc['code']).")
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerator[$row['patientId']] = $row;
		}

		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN medications ON medications.personId = patient.person_id
			INNER JOIN patientImmunizations ON patientImmunizations.patientId = patient.person_id
			WHERE {$initialPopulation} AND
				(
					patientImmunizations.series IN ('PR','MU','NA')
				)
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
		// TODO: add support for contraindication
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$exclusions = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$exclusions[$row['patientId']] = $row;
		}

		$nctr = count($numerator);
		$dctr = count($denominator);
		$xctr = count($exclusions);
		$percentage = self::calculatePerformanceMeasure($dctr,$nctr,$xctr);
		self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr,'exclusions'=>$xctr,'percentage'=>$percentage);
		return 'D: '.$dctr.'; N: '.$nctr.'; E: '.$xctr.'; P: '.$percentage;
	}

}
