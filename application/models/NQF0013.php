<?php
/*****************************************************************************
*       NQF0013.php
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


class NQF0013 extends NQF {

	protected static $results = array();

	public static function getResults() {
		return self::$results;
	}

	/*
	 * NQF0013: gov.cms.nqf.0013 (Core - 2)
	 * Title: Hypertension: Blood Pressure Measurement
	 * Description: Percentage of patient visits for patients aged 18 years and older with a diagnosis of hypertension who have been seen for at least 2 office visits, with blood pressure (BP) recorded.
	 */
	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dateStart = $this->dateStart;
		$dateEnd = $this->dateEnd;
		$providerId = (int)$this->providerId;

		$diagnosisCodes = array(
			// ICD9
			'401.0', '401.1', '401.9', '402.00', '402.01', '402.10', '402.11',
			'402.90', '402.91', '403.00', '403.01', '403.10', '403.11', '403.90',
			'403.91', '404.00', '404.01', '404.02', '404.03', '404.10', '404.11',
			'404.12', '404.13', '404.90', '404.91', '404.92', '404.93', 
			// SNOMED-CT
			'10562009', '10725009', '111438007', '1201005', '123799005', '123800009', '14973001',
			'15394000', '15938005', '16147005', '169465000', '18416000', '193003', '194774006',
			'194783001', '194785008', '194788005', '194791005', '194793008', '19769006', '198941007',
			'198942000', '198944004', '198945003', '198946002', '198947006', '198949009', '198951008',
			'198952001', '198953006', '198954000', '198956003', '198958002', '198959005', '198965005',
			'198966006', '198967002', '198968007', '198997005', '198999008', '199000005', '199002002',
			'199003007', '199005000', '199007008', '199008003', '206596003', '23130000', '23717007',
			'237279007', '237281009', '237282002', '23786008', '24042004', '26078007', '276789009',
			'28119000', '288250001', '29259002', '307632004', '308551004', '31407004', '31992008',
			'32916005', '34694006', '35303009', '367390009', '371125006', '37618003', '38481006',
			'39018007', '39727004', '397748008', '398254007', '41114007', '427889009', '428575007',
			'429198000', '429457004', '46481004', '46764007', '48146000', '48194001', '48552006',
			'49220004', '50490005', '52698002', '56218007', '57684003', '59621000', '59720008',
			'59997006', '62275004', '63287004', '65402008', '65443008', '65518004', '67359005',
			'70272006', '71874008', '72022006', '73030000', '73410007', '74451002', '78544004',
			'78808002', '78975002', '81626002', '8218002', '84094009', '86041002', '86234004',
			'8762007', '89242004', '9901000', 

		);

		$diagCodeList = $this->_formatCodeList($diagnosisCodes);
		$initialPopulation = "((DATE_FORMAT('{$dateEnd}','%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT('{$dateEnd}','00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) >= 18)";
		$initialPopulation .= "AND (encounter.date_of_treatment BETWEEN '{$dateStart}' AND '{$dateEnd}') AND encounter.treating_person_id = {$providerId}";
		// initial population AND >= 2 office visit
		$denominatorLookupTables = array(
			array(
				'join'=>'INNER JOIN problemLists ON problemLists.personId = patient.person_id',
				'where'=>"problemLists.code IN (".implode(',',$diagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN patientDiagnosis ON patientDiagnosis.patientId = patient.person_id',
				'where'=>"patientDiagnosis.code IN (".implode(',',$diagCodeList['code']).")",
			),
			array(
				'join'=>'INNER JOIN clinicalNotes ON clinicalNotes.personId = patient.person_id
					INNER JOIN genericData ON genericData.objectId = clinicalNotes.clinicalNoteId',
				'where'=>"(genericData.name = 'codeLookupICD9' AND (".implode(' OR ',$diagCodeList['generic'])."))",
			),
		);
		$denominator = array();
		foreach ($denominatorLookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			{$lookup['join']}
			WHERE {$initialPopulation} AND {$lookup['where']}
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$denominator[$row['patientId']] = $row;
			}
		}

		$numerator = array();
		foreach ($denominatorLookupTables as $lookup) {
			$sql = "SELECT patient.person_id AS patientId,
				patient.record_number AS MRN,
				COUNT(encounter.patient_id) AS visitCount
			FROM encounter
			INNER JOIN patient ON patient.person_id = encounter.patient_id
			INNER JOIN person ON person.person_id = patient.person_id
			{$lookup['join']}
			INNER JOIN vitalSignGroups ON vitalSignGroups.personId = patient.person_id
			INNER JOIN vitalSignValues ON vitalSignValues.vitalSignGroupId = vitalSignGroups.vitalSignGroupId
			WHERE {$initialPopulation} AND ({$lookup['where']}) AND
				(
					vitalSignValues.vital = 'bloodPressure' AND
					(vitalSignValues.value != '' AND vitalSignValues.value IS NOT NULL)
				)
			GROUP BY encounter.patient_id
			HAVING visitCount > 1";
			//file_put_contents('/tmp/pqri.sql',$sql.PHP_EOL.PHP_EOL,FILE_APPEND);
			$dbStmt = $db->query($sql);
			while ($row = $dbStmt->fetch()) {
				$numerator[$row['patientId']] = $row;
			}
		}
		$nctr = count($numerator);
		$dctr = count($denominator);
		$percentage = self::calculatePerformanceMeasure($dctr,$nctr);
		self::$results[] = array('denominator'=>$dctr,'numerator'=>$nctr,'percentage'=>$percentage);
		return 'D: '.$dctr.'; N: '.$nctr.'; P: '.$percentage;
	}

}
