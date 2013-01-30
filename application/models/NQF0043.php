<?php
/*****************************************************************************
*       NQF0043.php
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


class NQF0043 extends NQF {

	// TODO: to be reviewed against the pdf doc and genericData for ICD/CPT codes
	/*
	 * com.clearhealth.meaningfulUse.nqf0043-pqri111: CMS - 6
	 * Title: Pneumonia Vaccination Status for Older Adults
	 * Description: Percentage of patients 65 years of age and older who have ever received a pneumococcal vaccine.
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;

		$denominator = 0;
		$numerator = 0;
		$where = "((DATE_FORMAT(NOW(),'%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT(NOW(),'00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 65)";
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
			INNER JOIN patientImmunizations ON patientImmunizations.patientId = patient.person_id
			WHERE ".$where." AND
				patientImmunizations.immunization LIKE 'pneumococcal%'";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		if ($row = $db->fetchRow($sql)) {
			$numerator += (int)$row['ctr'];
		}
		return (($numerator / $denominator) * 100);
	}

}
