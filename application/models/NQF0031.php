<?php
/*****************************************************************************
*       NQF0031.php
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


class NQF0031 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0031-pqri112: CMS - 7
	 * Title: Breast Cancer Screening
	 * Description: Percentage of women 40-69 years of age who had a mammogram to screen for breast cancer.
	 */
	public function populate() {
		// NOTE: implemented based on pdf and xls docs
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$dateTimeStart = $this->dateStart.' 00:00:00';
		$dateTimeEnd = $this->dateEnd.' 23:59:59';
		$db = Zend_Registry::get('dbAdapter');
		$CPTCodes = array(
				'19180', '19200', '19220', '19240', '19303',
				'19304', '19305', '19306', '19307',
			);
		$CPTGenericCodeList = array();
		$CPTProcCodeList = array();
		foreach ($CPTCodes as $code) {
			$CPTGenericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$CPTProcCodeList[] = "'$code'";
		}
		$CPTGenericValue = '('.implode(' OR ',$CPTGenericCodeList).')';

		$biICD9Codes = array(
				'85.42', '85.44', '85.46', '85.48',
			);
		$uniICD9Codes = array(
				'85.41', '85.43', '85.45', '85.47',
			);
		$biICD9GenericCodeList = array();
		$biICD9DiagCodeList = array();
		foreach ($biICD9Codes as $code) {
			$biICD9GenericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$biICD9DiagCodeList[] = "'$code'";
		}
		$biICD9GenericValue = '('.implode(' OR ',$biICD9GenericCodeList).')';

		$uniICD9GenericCodeList = array();
		$uniICD9DiagCodeList = array();
		foreach ($uniICD9Codes as $code) {
			$uniICD9GenericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$uniICD9DiagCodeList[] = "'$code'";
		}
		$uniICD9GenericValue = '('.implode(' OR ',$uniICD9GenericCodeList).')';

		$denominator = 0;
		$numerator = 0;
		// two sources of CODES namely: patientDiagnosis/patientProcedures and genericData
		$where = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) BETWEEN 40 AND 69) AND
			((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(encounter.date_of_treatment,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(encounter.date_of_treatment,'00-%m-%d'))) <= 2) AND NOT
			(
				(
					(problemLists.code IN (".implode(',',$biICD9DiagCodeList).") AND (problemLists.dateOfOnset BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
					(patientDiagnosis.code IN (".implode(',',$biICD9DiagCodeList).") AND (patientDiagnosis.dateTime BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
					(genericData.name = 'codeLookupICD9' AND {$biICD9GenericValue}) OR
					(patientProcedures.code IN (".implode(',',$CPTProcCodeList).") AND (patientProcedures.dateTime BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
					(genericData.name = 'codeLookupCPT' AND {$CPTGenericValue})
				) OR
				( /* TODO: change query below to satisfy, >1 count(s) of “Procedure performed: unilateral mastectomy” */
					(problemLists.code IN (".implode(',',$uniICD9DiagCodeList).") AND (problemLists.dateOfOnset BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
					(patientDiagnosis.code IN (".implode(',',$uniICD9DiagCodeList).") AND (patientDiagnosis.dateTime BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
					(genericData.name = 'codeLookupICD9' AND {$uniICD9GenericValue}) OR
					(patientProcedures.code IN (".implode(',',$CPTProcCodeList).") AND (patientProcedures.dateTime BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
					(genericData.name = 'codeLookupCPT' AND {$CPTGenericValue})
				) /* TODO: AND NOT: FIRST “Procedure performed: unilateral mastectomy” = SECOND “Procedure performed: unilateral mastectomy” */
			)";
		$sql = "SELECT COUNT(DISTINCT patient.person_id) AS total
			FROM patient
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			INNER JOIN person ON person.person_id = patient.person_id
			LEFT JOIN problemLists ON problemLists.personId = patient.person_id
			LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = person.person_id
			LEFT JOIN patientProcedures ON patientProcedures.patientId = person.person_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = person.person_id
			LEFT JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE {$where}";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		if ($row = $db->fetchRow($sql)) {
			$denominator += (int)$row['total'];
		}

		$ret = 0;
		if ($denominator > 0) {
			$sql = "SELECT COUNT(DISTINCT patient.person_id) AS ctr
				FROM patient
				INNER JOIN encounter ON encounter.patient_id = patient.person_id
				INNER JOIN person ON person.person_id = patient.person_id
				LEFT JOIN problemLists ON problemLists.personId = patient.person_id
				LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = person.person_id
				LEFT JOIN patientProcedures ON patientProcedures.patientId = person.person_id
				INNER JOIN clinicalNotes ON clinicalNotes.personId = person.person_id
				INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
				WHERE {$where} AND
					(genericData.name = 'com.clearhealth.person.breastCancerScreening' AND genericData.value = '1')";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			if ($row = $db->fetchRow($sql)) {
				$numerator += (int)$row['ctr'];
			}
			$ret = (($numerator / $denominator) * 100);
		}
		return $ret;
	}

}
