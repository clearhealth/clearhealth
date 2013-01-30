<?php
/*****************************************************************************
*       NQF0061.php
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


class NQF0061 extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0061 / PQRI3: gov.cms.nqf.0061 (CMS - 3)
	 * Title: Diabetes: Blood Pressure Management
	 * Description: Percentage of patients 18 - 75 years of age with diabetes (type 1 or type 2) who had blood pressure <140/90 mmHg.
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$providerId = (int)$this->providerId;

		$diabetesCodes = array(
			// ICD9
			'250', '250.0', '250.00', '250.01', '250.02', '250.03', '250.10',
			'250.11', '250.12', '250.13', '250.20', '250.21', '250.22', '250.23',
			'250.30', '250.31', '250.32', '250.33', '250.4', '250.40', '250.41',
			'250.42', '250.43', '250.50', '250.51', '250.52', '250.53', '250.60',
			'250.61', '250.62', '250.63', '250.7', '250.70', '250.71', '250.72',
			'250.73', '250.8', '250.80', '250.81', '250.82', '250.83', '250.9',
			'250.90', '250.91', '250.92', '250.93', '357.2', '362.0', '362.01',
			'362.02', '362.03', '362.04', '362.05', '362.06', '362.07', '366.41',
			'648.0', '648.00', '648.01', '648.02', '648.03', '648.04', 
			// SNOMED-CT
			'111552007', '111558006', '11530004', '123763000', '127013003', '127014009', '190321005',
			'190328004', '190330002', '190331003', '190336008', '190353001', '190361006', '190368000',
			'190369008', '190371008', '190372001', '190383005', '190389009', '190390000', '190392008',
			'190406000', '190407009', '190410002', '190411003', '190412005', '190416001', '190417004',
			'190418009', '190419001', '190422004', '193184006', '197605007', '198609003', '199223000',
			'199227004', '199229001', '199230006', '199231005', '199234002', '201250006', '201251005',
			'201252003', '23045005', '230572002', '230577008', '237599002', '237600004', '237601000',
			'237604008', '237613005', '237618001', '237619009', '237627000', '25907005', '26298008',
			'267379000', '267380002', '2751001', '275918005', '28032008', '28453007', '290002008',
			'309426007', '310387003', '311366001', '312912001', '313435000', '313436004', '314537004',
			'314771006', '314772004', '314893005', '314902007', '314903002', '33559001', '34140002',
			'359611005', '359638003', '359642000', '360546002', '371087003', '38542009', '39058009',
			'39181008', '408539000', '408540003', '413183008', '414890007', '414906009', '420414003',
			'420422005', '421750000', '421847006', '421895002', '422183001', '422228004', '422275004',
			'423263001', '424736006', '424989000', '425159004', '425442003', '426705001', '426875007',
			'427089005', '428896009', '42954008', '44054006', '4627003', '46635009', '50620007',
			'51002006', '5368009', '54181000', '57886004', '59079001', '5969009', '70694009',
			'73211009', '74263009', '75524006', '75682002', '76751001', '81531005', '81830002',
			'8801005', '91352004', '9859006', 
		);
		$diagCodeList = $this->_formatCodeList($diabetesCodes);

		$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) BETWEEN 18 AND 75)";
		$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";

		// denominator = initial patient population + denominator
		$initialPopulation .= " AND
				(
					(
						problemLists.code IN (".implode(',',$diagCodeList['code']).") AND
						((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(problemLists.dateOfOnset,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(problemLists.dateOfOnset,'00-%m-%d'))) <= 2)
					) OR
					(
						patientDiagnosis.code IN (".implode(',',$diagCodeList['code']).") AND
						((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(patientDiagnosis.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(patientDiagnosis.dateTime,'00-%m-%d'))) <= 2)
					) OR
					(
						(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diagCodeList['generic']).")) AND
						((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(genericData.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(genericData.dateTime,'00-%m-%d'))) <= 2)
					)
				)";
		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN
			FROM patient
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			LEFT JOIN problemLists ON problemLists.personId = patient.person_id
			LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE {$initialPopulation}";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$denominator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$denominator[$row['patientId']] = $row;
		}

		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN
			FROM vitalSignGroups
			INNER JOIN patient ON patient.person_id = vitalSignGroups.personId
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			INNER JOIN vitalSignValues ON vitalSignValues.vitalSignGroupId = vitalSignGroups.vitalSignGroupId
			LEFT JOIN problemLists ON problemLists.personId = patient.person_id
			LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE {$initialPopulation} AND
				vitalSignValues.vital = 'bloodPressure' AND
				SUBSTRING_INDEX(vitalSignValues.value,'/',1) < 140 AND
				SUBSTRING_INDEX(vitalSignValues.value,'/',-1) < 90
			GROUP BY vitalSignGroups.personId
			ORDER BY vitalSignGroups.dateTime DESC";
		//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
		$numerator = array();
		$dbStmt = $db->query($sql);
		while ($row = $dbStmt->fetch()) {
			$numerator[$row['patientId']] = $row;
		}

		$polycysticCodes = array(
			// ICD9
			'256.4',
			// SNOMED-CT
			'69878008',
		);
		$polyDiagCodeList = $this->_formatCodeList($polycysticCodes);

		$gestationalCodes = array(
			// ICD9
			'648.8', '648.80', '648.81', '648.82', '648.83', '648.84',
			// SNOMED-CT
			'11687002', '420491007', '420738003', '420989005', '421223006', '421389009', '421443003',
			'422155003', '46894009', '71546005', '75022004',
		);

		$steroidCodes = array(
			// ICD9
			'249', '249.0', '249.00', '249.01', '249.1', '249.10', '249.11',
			'249.2', '249.20', '249.21', '249.3', '249.30', '249.31', '249.4',
			'249.40', '249.41', '249.5', '249.50', '249.51', '249.6', '249.60',
			'249.61', '249.7', '249.70', '249.71', '249.8', '249.80', '249.81',
			'249.9', '249.90', '249.91', '251.8', '962.0',
			// SNOMED-CT
			'190416008', '190447002', '53126001',
		);
		$gesteroidDiagCodeList = $this->_formatCodeList(array_merge($gestationalCodes,$steroidCodes));

		$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN
			FROM patient
			INNER JOIN person ON person.person_id = patient.person_id
			INNER JOIN encounter ON encounter.patient_id = patient.person_id
			LEFT JOIN problemLists ON problemLists.personId = patient.person_id
			LEFT JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id
			LEFT JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
			INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId
			WHERE {$initialPopulation} AND
				(
					(
						(
							problemLists.code IN (".implode(',',$polyDiagCodeList['code']).") OR
							patientDiagnosis.code IN (".implode(',',$polyDiagCodeList['code']).") OR
							(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$polyDiagCodeList['generic'])."))
						) AND
						(
							(
								problemLists.code NOT IN (".implode(',',$diagCodeList['code']).")) AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(problemLists.dateOfOnset,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(problemLists.dateOfOnset,'00-%m-%d'))) <= 2)
							) OR
						(
							(
								patientDiagnosis.code NOT IN (".implode(',',$diagCodeList['code']).")) AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(patientDiagnosis.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(patientDiagnosis.dateTime,'00-%m-%d'))) <= 2)
							) OR
							(
								genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$diagCodeList['generic']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(genericData.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(genericData.dateTime,'00-%m-%d'))) <= 2)
							)
					) OR
					(
						(
							(
								problemLists.code IN (".implode(',',$gesteroidDiagCodeList['code']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(problemLists.dateOfOnset,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(problemLists.dateOfOnset,'00-%m-%d'))) <= 2)
							) OR
							(
								patientDiagnosis.code IN (".implode(',',$gesteroidDiagCodeList['code']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(patientDiagnosis.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(patientDiagnosis.dateTime,'00-%m-%d'))) <= 2)
							) OR
							(
								genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$gesteroidDiagCodeList['generic']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(genericData.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(genericData.dateTime,'00-%m-%d'))) <= 2)
							)
						) AND
						(
							(
								problemLists.code NOT IN (".implode(',',$diagCodeList['code']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(problemLists.dateOfOnset,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(problemLists.dateOfOnset,'00-%m-%d'))) <= 2)
							) OR
							(
								patientDiagnosis.code NOT IN (".implode(',',$diagCodeList['code']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(patientDiagnosis.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(patientDiagnosis.dateTime,'00-%m-%d'))) <= 2)
							) OR
							(
								genericData.name = 'codeLookupICD9' AND NOT (".implode(' OR ',$diagCodeList['generic']).") AND
								((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(genericData.dateTime,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(genericData.dateTime,'00-%m-%d'))) <= 2)
							)
						)
					)
				)";
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
