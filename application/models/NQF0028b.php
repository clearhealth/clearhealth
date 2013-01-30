<?php
/*****************************************************************************
*       NQF0028b.php
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


class NQF0028b extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0028b: gov.cms.nqf.0028b (Core - 3b)
	 * Title: Preventive Care and Screening Measure Pair: Tobacco Cessation Intervention
	 * Description: Percentage of patients aged 18 years and older identified as tobacco users within the past 24 months and have been seen for at least 2 office visits, who received cessation intervention.
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$providerId = (int)$this->providerId;

		$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 18)";
		$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";
		// initial population AND >= 2 office visit

		// denominator = initial patient population + denominator
		$initialPopulation .= " AND
				genericData.name = 'com.clearhealth.smokingStatus' AND
				((PERIOD_DIFF(DATE_FORMAT(encounter.date_of_treatment,'%Y%m'),DATE_FORMAT(genericData.dateTime,'%Y%m')) - (DAY(encounter.date_of_treatment) < DAY(genericData.dateTime))) <= 24)";
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE {$initialPopulation}
			GROUP BY encounter.patient_id, genericData.objectId
			HAVING visitCount > 1
			ORDER BY genericData.dateTime DESC";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$denominator = array();
		$numerator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$denominator[$row['patientId']] = $row;
			$sql = "SELECT encounter.patient_id AS patientId, genericData.value AS value
				FROM genericData
				INNER JOIN clinicalNotes ON clinicalNotes.clinicalNoteId = genericData.objectId
				INNER JOIN encounter ON encounter.patient_id = clinicalNotes.personId
				WHERE encounter.patient_id = {$row['patientId']} AND
				(
					genericData.name = 'com.clearhealth.smokingCessation' AND
					(
						(PERIOD_DIFF(DATE_FORMAT(encounter.date_of_treatment,'%Y%m'),DATE_FORMAT(genericData.dateTime,'%Y%m')) - (DAY(encounter.date_of_treatment) < DAY(genericData.dateTime))) <= 24
					)
				)
				ORDER BY genericData.dateTime DESC
				LIMIT 1";
			if ($tmp = $db->fetchRow($sql)) {
				if ($tmp['value'] == 1) $numerator[$row['patientId']] = $row;
			}
		}
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$nctr = count($numerator);
		$dctr = count($denominator);
		$percentage = self::calculatePerformanceMeasure($dctr,$nctr);
		self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr,'percentage'=>$percentage);
		return 'D: '.$dctr.'; N: '.$nctr.'; P: '.$percentage;
	}

}
