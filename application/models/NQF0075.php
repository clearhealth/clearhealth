<?php
/*****************************************************************************
*       NQF0075.php
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


class NQF0075 extends NQF {

	/*
	 * com.clearhealth.meaningfulUse.nqf0075: CMS - 37
	 * Title: Ischemic Vascular Disease (IVD): Complete Lipid Panel and LDL Control
	 * Description: Percentage of patients 18 years of age and older who were discharged alive for acute myocardial infarction (AMI), coronary artery bypass graft (CABG) or percutaneous transluminal angioplasty (PTCA) from January 1-November1 of the year prior to the measurement year, or who had a diagnosis of ischemic vascular disease (IVD) during the measurement year and the year prior to the measurement year and who had a complete lipid profile performed during the measurement year and whose LDL-C<100 mg/dL.
	 */
	public function populate() {
		// NOTE: implemented based on pdf and xls docs
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$dateTimeStart = $this->dateStart.' 00:00:00';
		$dateTimeEnd = $this->dateEnd.' 23:59:59';
		$db = Zend_Registry::get('dbAdapter');
		$CPTCodes = array(
				// PTCA CPT
				'33140', '92980', '92982', '92995',
			);
		$CPTGenericCodeList = array();
		$CPTProcCodeList = array();
		foreach ($CPTCodes as $code) {
			$CPTGenericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$CPTProcCodeList[] = "'$code'";
		}
		$CPTGenericValue = '('.implode(' OR ',$CPTGenericCodeList).')';
		$ICD9Codes = array(
				// PTCA ICD9
				'00.66', '36.06', '36.07', '36.09',
				// IVD ICD9
				'434.00', '411.0', '411.1', '411.81', '411.89',
				'413.0', '413.1', '413.9', '414.00', '414.01',
				'414.02', '414.03', '414.04', '414.05', '414.06',
				'414.07', '414.2', '414.8', '414.9', '429.2',
				'433.0', '433.01', '433.10', '433.11', '433.20',
				'433.21', '433.30', '433.31', '433.80', '433.81',
				'433.90', '433.91', '434.01', '434.10', '434.11',
				'434.90', '434.91', '440.1', '440.20', '440.21',
				'440.22', '440.23', '440.24', '440.29', '440.4',
				'444.0', '444.1', '444.21', '444.22', '444.81',
				'444.89', '444.9', '445.01', '445.02', '445.8',
				'445.81',
			);
		$ICD9GenericCodeList = array();
		$ICD9DiagCodeList = array();
		foreach ($ICD9Codes as $code) {
			$ICD9GenericCodeList[] = "genericData.value LIKE '%1-{$code} - %'";
			$ICD9DiagCodeList[] = "'$code'";
		}
		$ICD9GenericValue = '('.implode(' OR ',$ICD9GenericCodeList).')';

		$denominator = 0;
		$numerator = 0;
		$where = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 18) AND
			(
				(problemLists.code IN (".implode(',',$ICD9DiagCodeList).") AND (problemLists.dateOfOnset BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
				(patientDiagnosis.code IN (".implode(',',$ICD9DiagCodeList).") AND (patientDiagnosis.dateTime BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
				(genericData.name = 'codeLookupICD9' AND {$ICD9GenericValue}) OR
				(patientProcedures.code IN (".implode(',',$CPTProcCodeList).") AND (patientProcedures.dateTime BETWEEN '{$dateTimeStart}' AND '{$dateTimeEnd}')) OR
				(genericData.name = 'codeLookupCPT' AND {$CPTGenericValue})
			)";
		$sql = "SELECT COUNT(DISTINCT patient.person_id) AS total
			FROM patient
			INNER JOIN person ON person.person_id = patient.person_id
			LEFT JOIN problemLists ON problemLists.personId = patient.person_id
			LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id
			LEFT JOIN patientProcedures ON patientProcedures.patientId = patient.person_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			LEFT JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE ".$where;
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		if ($row = $db->fetchRow($sql)) {
			$denominator += (int)$row['total'];
		}

		$sql = "SELECT person.person_id AS patientId,
				lab_order.lab_order_id AS labOrderId,
				lab_result.description AS description,
				lab_result.value AS value,
				lab_result.units AS units
			FROM lab_order
			INNER JOIN lab_test ON lab_test.lab_order_id = lab_order.lab_order_id
			INNER JOIN lab_result ON lab_result.lab_test_id = lab_test.lab_test_id
			INNER JOIN person ON person.person_id = lab_order.patient_id
			LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = person.person_id
			LEFT JOIN patientProcedures ON patientProcedures.patientId = person.person_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = person.person_id
			LEFT JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE ".$where." AND
				(
					lab_result.description LIKE '%LDL%' OR
					(
						lab_result.description LIKE '%HDL%' OR /* instead of AND */
						lab_result.description LIKE '%total cholesterol%' OR /* instead of AND */
						lab_result.description LIKE '%triglycerides%'
					)
				)
			GROUP BY lab_result.lab_result_id";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$patients = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			if (!isset($patients[$row['patientId']])) $patients[$row['patientId']] = array();
			if (!isset($patients[$row['patientId']][$row['labOrderId']])) $patients[$row['patientId']][$row['labOrderId']] = array();
			$patients[$row['patientId']][$row['labOrderId']][] = $row;
		}
		$numerators = array();
		$numerators[] = 0;
		$numerators[] = 0;

		foreach ($patients as $id=>$labResults) {
			$numerators[0]++;
			foreach ($labResults as $labOrderId=>$rows) {
				$hdl = null;
				$cholesterol = null;
				$triglycerides = null;
				foreach ($rows as $row) {
					if (stripos($row['description'],'LDL') !== false && $row['value'] < 100) {
						$numerators[1]++;
						break 2;
					}
					else if (stripos($row['description'],'HDL') !== false) {
						$hdl = $row['value'];
					}
					else if (stripos($row['description'],'total cholesterol') !== false) {
						$cholesterol = $row['value'];
					}
					else if (stripos($row['description'],'triglycerides') !== false) {
						$triglycerides = $row['value'];
					}
				}
				if ($hdl !== null && $cholesterol !== null &&
				    $triglycerides !== null && (($cholesterol - $hdl - $triglycerides) / 5) < 100) {
					$numerators[1]++;
					break;
				}
			}
		}
		$dbStmt->closeCursor();

		$ret = array();
		foreach ($numerators as $key=>$value) {
			$ret[] = 'Num'.($key+1).': '.(($value / $denominator) * 100);
		}
		return implode("<br/>\n",$ret);
	}

}
