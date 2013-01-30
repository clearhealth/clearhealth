<?php
/*****************************************************************************
*       NQF0032.php
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


class NQF0032 extends NQF {

	// TODO
	/*
	 * com.clearhealth.meaningfulUse.nqf0032: CMS - 33
	 * Title: Cervical Cancer Screening
	 * Description: Percentage of women 21-64 years of age, who received one or more Pap tests to screen for cervical cancer
	 */
	// TODO: to be reviewed against the pdf doc and genericData for ICD/CPT codes
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;

		$denominator = 0;
		$numerator = 0;
		$where = "((DATE_FORMAT(NOW(),'%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT(NOW(),'00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) BETWEEN 21 AND 64) AND
			(person.gender = '2' OR person.gender = 'F')";
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
			WHERE ".$where." AND
				lab_result.description LIKE 'Pap%'";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		if ($row = $db->fetchRow($sql)) {
			$numerator += (int)$row['ctr'];
		}
		return (($numerator / $denominator) * 100);
	}

}
