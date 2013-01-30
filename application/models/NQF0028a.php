<?php
/*****************************************************************************
*       NQF0028a.php
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


class NQF0028a extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0028a: gov.cms.nqf.0028a (Core - 3a)
	 * Title: Preventive Care and Screening Measure Pair: Tobacco Use Assessment
	 * Description: Percentage of patients aged 18 years and older who have been seen for at least 2 office visits who were queried about tobacco use one or more times within 24 months
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$providerId = (int)$this->providerId;

		$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 18)";
		$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";
		// initial population AND >= 2 office visit
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			WHERE {$initialPopulation}
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$denominator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$denominator[$row['patientId']] = $row;
		}

		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE {$initialPopulation} AND
				genericData.name = 'com.clearhealth.smokingStatus' AND
				((PERIOD_DIFF(DATE_FORMAT(encounter.date_of_treatment,'%Y%m'),DATE_FORMAT(genericData.dateTime,'%Y%m')) - (DAY(encounter.date_of_treatment) < DAY(genericData.dateTime))) <= 24)
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerator[$row['patientId']] = $row;
		}
		$nctr = count($numerator);
		$dctr = count($denominator);
		$percentage = self::calculatePerformanceMeasure($dctr,$nctr);
		self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr,'percentage'=>$percentage);
		return 'D: '.$dctr.'; N: '.$nctr.'; P: '.$percentage;
	}

}
