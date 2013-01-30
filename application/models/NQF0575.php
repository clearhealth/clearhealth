<?php
/*****************************************************************************
*       NQF0575.php
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


class NQF0575 extends NQF {

	/*
	 * com.clearhealth.meaningfulUse.nqf0575: CMS - 38
	 * Title: Diabetes: Hemoglobin A1c Control (<8.0%)
	 * Description: The percentage of patients 18-75 years of age with diabetes (type 1 or type 2) who had hemoglobin A1c <8.0%.
	 */
	// TODO: check for diabetes
	// TODO: to be reviewed against the pdf doc and genericData for ICD/CPT codes
	public function populate() {
		// Format: "1-(codes) - "
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$diabetesICD9Codes = array(
				'250', '250.0', '250.00', '250.01', '250.02',
				'250.03', '250.10', '250.11', '250.12', '250.13',
				'250.20', '250.21', '250.22', '250.23', '250.30',
				'250.31', '250.32', '250.33', '250.4', '250.40',
				'250.41', '250.42', '250.43', '250.50', '250.51',
				'250.52', '250.53', '250.60', '250.61', '250.62',
				'250.63', '250.7', '250.70', '250.71', '250.72',
				'250.73', '250.8', '250.80', '250.81', '250.82',
				'250.83', '250.9', '250.90', '250.91', '250.92',
				'250.93', '357.2', '362.0', '362.01', '362.02',
				'362.03', '362.04', '362.05', '362.06', '362.07',
				'366.41', '648.0', '648.00', '648.01', '648.02',
				'648.03', '648.04'
			);
		$genericCodeList = array();
		foreach ($diabetesICD9Codes as $code) {
			$genericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
		}
		$genericValue = '('.implode(' OR ',$genericCodeList).')';

		$db = Zend_Registry::get('dbAdapter');

		$denominator = 0;
		$numerator = 0;
		$where = "((DATE_FORMAT(NOW(),'%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT(NOW(),'00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) BETWEEN 18 AND 75)";
		$sql = "SELECT COUNT(DISTINCT patient.person_id) AS total
			FROM patient
			INNER JOIN person ON person.person_id = patient.person_id
			WHERE ".$where;
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		if ($row = $db->fetchRow($sql)) {
			$denominator += (int)$row['total'];
		}
		$sql = "SELECT COUNT(DISTINCT patient.person_id) AS ctr
			FROM patient
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN lab_order ON lab_order.patient_id = patient.person_id
			INNER JOIN lab_test ON lab_test.lab_order_id = lab_order.lab_order_id
			INNER JOIN lab_result ON lab_result.lab_test_id = lab_test.lab_test_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			LEFT JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE ".$where." AND
				((lab_result.description LIKE '%HbA1c%' AND
				lab_result.value < 8) OR (genericData.name = 'codeLookupICD9' AND {$genericValue}))";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		if ($row = $db->fetchRow($sql)) {
			$numerator += (int)$row['ctr'];
		}
		return (($numerator / $denominator) * 100);
	}

}
