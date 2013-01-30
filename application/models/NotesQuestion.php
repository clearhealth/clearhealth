<?php
/*****************************************************************************
*       NotesQuestion.php
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


class NotesQuestion implements NSDRMethods {

	function nsdrPersist($tthis,$context,$data) {
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$ret = 'Unknown';
		$dateStart = date('Y-m-d 00:00:00');
		$dateEnd = date('Y-m-d 23:59:59');
		$namespace = $tthis->_nsdrNamespace;
		$attributes = $tthis->_attributes;
		if (isset($attributes['dateStart'])) {
			$dateStart = date('Y-m-d 00:00:00',strtotime($attributes['dateStart']));
		}
		if (isset($attributes['dateEnd'])) {
			$dateEnd = date('Y-m-d 23:59:59',strtotime($attributes['dateEnd']));
		}
		if (isset($attributes['namespace'])) {
			$namespace = $attributes['namespace'];
		}
		$db = Zend_Registry::get('dbAdapter');
		$sql = "SELECT `genericData`.`value` AS `gdValue` FROM `genericData`
			INNER JOIN `clinicalNotes` ON clinicalNotes.clinicalNoteId=genericData.objectId
			INNER JOIN `patient` ON patient.person_id=clinicalNotes.personId
			INNER JOIN (SELECT MAX(revisionId) AS `revisionId` FROM `genericData` GROUP BY `objectId`) AS `grp` ON grp.revisionId=genericData.revisionId
			WHERE (clinicalNotes.dateTime >= '{$dateStart}') AND (clinicalNotes.dateTime <= '{$dateEnd}') AND (patient.record_number = '{$context}') AND (genericData.name = '{$namespace}') AND (genericData.objectClass = 'ClinicalNote') ";
		if ($rows = $db->fetchAll($sql)) {
			$ret = 'No';
			foreach ($rows as $row) {
				if ((int)$row['gdValue'] == 1) {
					$ret = 'Yes';
					break;
				}
			}
			return $ret;
		}
		$sql = "SELECT lab_order.lab_order_id
			FROM lab_order
			INNER JOIN lab_test ON lab_test.lab_order_id = lab_order.lab_order_id
			INNER JOIN lab_result ON lab_result.lab_test_id = lab_test.lab_test_id
			INNER JOIN person ON person.person_id = lab_order.patient_id
			INNER JOIN patient ON patient.person_id = person.person_id
			WHERE lab_result.description LIKE '%{$namespace}%' AND lab_result.observation_time >= '{$dateStart}' AND lab_result.observation_time <= '{$dateEnd}' AND patient.record_number = '{$context}' ORDER BY lab_result.observation_time ASC LIMIT 1";
		if ($row = $db->fetchRow($sql)) {
			$ret = 'Yes';
			return $ret;
		}
		$sql = "SELECT patientDiagnosis.code
			FROM patientDiagnosis
			WHERE patientDiagnosis.code LIKE '%{$namespace}%' OR patientDiagnosis.diagnosis LIKE '%{$namespace}%' AND patientDiagnosis.dateTime >= '{$dateStart}' AND patientDiagnosis.dateTime <= '{$dateEnd}' LIMIT 1";
		if ($row = $db->fetchRow($sql)) {
			$ret = 'Yes';
		}
		return $ret;
	}

	public function nsdrMostRecent($tthis,$context,$data) {
	}

	public static function getResultSinceHIV($tthis,$context,$namespace) {
		$ret = 'Unknown';
		$dateStart = date('Y-m-d 00:00:00');
		$dateEnd = date('Y-m-d 23:59:59');
		$attributes = $tthis->_attributes;
		if (isset($attributes['dateStart'])) {
			$dateStart = date('Y-m-d 00:00:00',strtotime($attributes['dateStart']));
		}
		if (isset($attributes['dateEnd'])) {
			$dateEnd = date('Y-m-d 23:59:59',strtotime($attributes['dateEnd']));
		}
		$db = Zend_Registry::get('dbAdapter');

		$sql = "SELECT `genericData`.`value` AS `gdValue` FROM `genericData`
			INNER JOIN `clinicalNotes` ON clinicalNotes.clinicalNoteId=genericData.objectId
			INNER JOIN `patient` ON patient.person_id=clinicalNotes.personId
			INNER JOIN (SELECT MAX(revisionId) AS `revisionId` FROM `genericData` GROUP BY `objectId`) AS `grp` ON grp.revisionId=genericData.revisionId
			WHERE (clinicalNotes.dateTime >= (
				SELECT DATE_FORMAT(lab_result.observation_time,'%m/%d/%Y') as 'date' FROM lab_order  inner JOIN lab_test on lab_test.lab_order_id = lab_order.lab_order_id inner join lab_result on lab_result.lab_test_id = lab_test.lab_test_id inner join person on person.person_id = lab_order.patient_id inner join patient on patient.person_id = person.person_id where lab_result.description LIKE '%CD4%' AND lab_result.observation_time >= '{$dateStart}' AND lab_result.observation_time <= '{$dateEnd}' and patient.record_number = '{$context}' LIMIT 1
			))
			AND (patient.record_number = '{$context}') AND (genericData.name = '{$namespace}') AND (genericData.objectClass = 'ClinicalNote') ";
		if ($rows = $db->fetchAll($sql)) {
			$ret = 'No';
			foreach ($rows as $row) {
				if ((int)$row['gdValue'] == 1) {
					$ret = 'Yes';
					break;
				}
			}
		}
		return $ret;
	}

}
