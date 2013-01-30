<?php
/*****************************************************************************
*       VisitIterator.php
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


class VisitIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null) {
		parent::__construct("Visit",$dbSelect);
	}

	function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()->from('encounter',array('*','DATE_FORMAT(date_of_treatment,"%Y-%m-%d") AS date_of_treatment'));
		$dbSelect->joinLeft('person', 'person.person_id = encounter.treating_person_id',array('concat(person.last_name, ", ", person.first_name, " ", person.middle_name) as providerDisplayName'));
		$dbSelect->joinLeft('buildings', 'encounter.building_id = buildings.id', array('buildings.name as locationName'));
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'patientId':
					$dbSelect->where('encounter.patient_id = ?',(int)$value);
					break;
				case 'dateRange':
					$val = explode(':',$value);
					$dbSelect->where("encounter.date_of_treatment BETWEEN '{$val[0]} 00:00:00' AND '{$val[1]} 23:59:59'");
					break;
				case 'facilityId':
					if ($value) $dbSelect->where('encounter.room_id = ?',(int)$value);
					break;
				case 'payerId':
					if ($value) $dbSelect->where('encounter.activePayerId = ?',(int)$value);
					break;
				case 'providerId':
					if ($value) $dbSelect->where('encounter.treating_person_id = ?',(int)$value);
					break;
				case 'userId':
					if ($value) $dbSelect->where('encounter.created_by_user_id = ?',(int)$value);
					break;
				case 'facilities':
					// practice, building, room
					if (!is_array($value)) $value = array($value);
					$facilities = array();
					foreach ($value as $val) {
						$facilities[] = 'encounter.practice_id = '.(int)$val['practice'].' AND encounter.building_id = '.(int)$val['building'].' AND encounter.room_id = '.(int)$val['room'];
					}
					$dbSelect->where(implode(' OR ',$facilities));
					break;
				case 'facility':
					// practice, building, room
					$dbSelect->where('encounter.practice_id = ?',(int)$value['practice']);
					$dbSelect->where('encounter.building_id = ?',(int)$value['building']);
					$dbSelect->where('encounter.room_id = ?',(int)$value['room']);
					break;
				case 'payers':
					$payers = array();
					foreach ($value as $payerId) {
						$payers[] = (int)$payerId;
					}
					$dbSelect->where('encounter.activePayerId IN ('.implode(',',$payers).')');
					break;
				case 'openClosed':
					if ($value == '0') $dbSelect->where('encounter.closed = 0');
					else if ($value == '1') $dbSelect->where('encounter.closed = 1');
					break;
				case 'visitId':
					$dbSelect->where('encounter.encounter_id = ?',(int)$value);
					break;
				case 'batchHistoryId':
					if (!is_array($value)) {
						$claimIds = $value;
						$value = array();
						foreach (explode(',',$claimIds) as $claimId) {
							$value[] = (int)$claimId;
						}
					}
					$dbSelect->join('claimLines','claimLines.visitId = encounter.encounter_id')
						->where('claimLines.claimId IN ('.implode(',',$value).')');
					break;
				case 'void':
					$dbSelect->where('encounter.void = ?',(int)$value);
					break;
				case 'mrn':
					$dbSelect->join('patient','patient.person_id = encounter.patient_id')
						->where('patient.record_number = ?',$value);
					break;
			}
		}
		$dbSelect->order('date_of_treatment DESC');
		//$dbSelect->where("building_id = ?", $filters['locationId']);
		trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
