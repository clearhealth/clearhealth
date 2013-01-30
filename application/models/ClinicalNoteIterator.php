<?php
/*****************************************************************************
*       ClinicalNoteIterator.php
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


class ClinicalNoteIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null) {
		parent::__construct("ClinicalNote",$dbSelect);
	}

	public function current() {
		$ormObj = new $this->_ormClass();
		$row = $this->_dbStmt->fetch(null,null,$this->_offset);
		return $row;
		$ormObj->populateWithArray($row);
		return $ormObj;
	}

	public function setFilter($type,Array $filter) {
		if (count($filter) <= 0) {
			return;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $this->_getDefaultSelect();
		$dbSelect->where('clinicalNotes.personId = ' . (int)$filter['personId']);
		switch ($type) {
			case 'byAuthoringPersonId': // By Authoring Person
				$byAuthoringPersonId = (int)$filter['authoringPersonId'];
				if ($byAuthoringPersonId > 0) { // Signed Notes By Author
					$dbSelect->where('clinicalNotes.authoringPersonId = ' . $byAuthoringPersonId);
					$dbSelect->where('clinicalNotes.eSignatureId > 0');
				}
				break;
			case 'byDateRange': // By Date Range
				//else if (strlen($byDateRange) > 0) { // Signed Notes By Date Range
				$dateRange = explode('^',$filter['dateRange']);
				if (count($dateRange) == 2) {
					// increment by one day, right? because query using between has
					$dateRange[1] = date('Y-m-d', strtotime('+1 day', strtotime($dateRange[1])));
					$dbSelect->where("clinicalNotes.dateTime BETWEEN ? AND '{$dateRange[1]}'",$dateRange[0]);
					$dbSelect->where('clinicalNotes.eSignatureId > 0');
				}
				break;
			case 'byAllSigned': // By All Signed Notes
				$dbSelect->where('clinicalNotes.eSignatureId > 0');
				break;
			case 'byAllUserUncosigned': // By All Uncosigned Notes for [user]
				$dbSelect->where('clinicalNotes.authoringPersonId = ' . (int)Zend_Auth::getInstance()->getIdentity()->personId);
				$dbSelect->where('clinicalNotes.eSignatureId = 2'); // is it 2 for uncosigned?
				break;
			case 'byAllUserUnsigned': // By All Unsigned Notes for [user]
				$dbSelect->where('clinicalNotes.authoringPersonId = ' . (int)Zend_Auth::getInstance()->getIdentity()->personId);
				$dbSelect->where('clinicalNotes.eSignatureId = 0'); // is it 0 for unsigned?
				break;
			case 'byAllAuthorsUnsigned': // By All Unsigned Notes for All Authors
				$dbSelect->where('clinicalNotes.eSignatureId = 0'); // is it 0 for unsigned?
				break;
			case 'byCurrentPractice': // By Current Practice
				$dbSelect->where('clinicalNotes.locationId = '.(int)$filter['locationId']); // is it 0 for unsigned?
				break;
			case 'bySelectedVisit': // By Selected Visit
				$selectedVisit = date('Y-m-d',strtotime($filter['selectedVisit']));
				$dbSelect->where("clinicalNotes.dateTime BETWEEN '$selectedVisit 00:00:00' AND '$selectedVisit 23:59:59'");
				break;
			case 'byVisitPractice': // Notes by Visits to Current Practice
				$dbSelect->joinLeft('encounter','encounter.encounter_id = clinicalNotes.visitId')
					->where('encounter.practice_id = ?',(int)$filter['practiceId']);
				break;
			case 'byVisitBuilding': // Notes by Visits to Current Building
				$dbSelect->joinLeft('encounter','encounter.encounter_id = clinicalNotes.visitId')
					->where('encounter.building_id = ?',(int)$filter['buildingId']);
				break;
			default: // Default: Last 100 Notes
				$dbSelect->order('clinicalNotes.clinicalNoteId DESC')
			 	 	 ->limit(100);
				break;
		}
		$dbSelect->order('clinicalNotes.eSignatureId ASC')
			 ->order('clinicalNotes.dateTime DESC');

		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

	public function customView(Array $custom) {
		if (count($custom) <= 0) {
			return;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $this->_getDefaultSelect();
		$dbSelect->where('clinicalNotes.personId = ' . (int)$custom['personId']);
		// check if required status exists
		if (isset($custom['status'])) {
			$status = $custom['status']; // signed; unsigned; uncosigned
			switch ($status) {
				case 'signed':
					$dbSelect->where('clinicalNotes.eSignatureId > 0');
					break;
				case 'unsigned':
					$dbSelect->where('clinicalNotes.eSignatureId = 0');
					break;
				case 'uncosigned':
					//$dbSelect->where('clinicalNotes.eSignatureId > 0');
					break;
			}
			$authoringPersonId = (int)$custom['authoringPersonId'];
			if ($authoringPersonId > 0) {
				$dbSelect->where('clinicalNotes.authoringPersonId = ' . $authoringPersonId);
			}
			$filterDate = isset($custom['filterDate'])?$custom['filterDate']:''; // on or undefined
			if (strlen($filterDate) > 0) {
				$dateBegin = $custom['dateBegin'];
				$dateEnd = $custom['dateEnd'];
				// increment by one day, right? because query using between has
				$dateEnd = date('Y-m-d', strtotime('+1 day', strtotime($dateEnd)));
				$dbSelect->where("clinicalNotes.dateTime BETWEEN ? AND '{$dateEnd}'",$dateBegin);
			}
			$filterResults = $custom['filterResults'];
			if (strlen($filterResults) > 0) {
				// enclosed filter results
				$filterResults = '%'.$filterResults.'%';
				$inTitles = $custom['inTitles'];
				if (strlen($inTitles) > 0) {
					$dbSelect->where('clinicalNoteDefinitions.title LIKE ?',$filterResults);
				}
				$inSubjects = $custom['inSubjects'];
				if (strlen($inSubjects) > 0) {
					$dbSelect->where('clinicalNoteTemplates.name LIKE ?',$filterResults);
				}
			}
			$groupDoc = isset($custom['groupDoc'])?$custom['groupDoc']:'';
			if (strlen($groupDoc) > 0) {
				$groupBy = $custom['groupBy']; // groupDateVisit; groupLocation; groupTitle; groupAuthor
				switch ($groupBy) {
					case 'groupDateVisit':
						$dbSelect->group('clinicalNotes.visitId');
						break;
					case 'groupLocation':
						$dbSelect->group('clinicalNotes.locationId');
						break;
					case 'groupTitle':
						$dbSelect->group('clinicalNoteDefinitions.title');
						break;
					case 'groupAuthor':
						$dbSelect->group('clinicalNotes.authoringPersonId');
						break;
				}
			}
			$sortDoc = $custom['sortDoc']; // chronological; reverse-chronological
			$sqlSort = 'ASC';
			if ($sortDoc != 'chronological') {
				$sqlSort = 'DESC';
			}
			$sortBy = $custom['sortBy']; // sortDateNote; sortTitle; sortAuthor; sortLocation
			switch ($sortBy) {
				case 'sortDateNote':
					$dbSelect->order('clinicalNotes.dateTime '.$sqlSort);
					break;
				case 'sortTitle':
					$dbSelect->order('clinicalNoteDefinitions.title '.$sqlSort);
					break;
				case 'sortAuthor':
					$dbSelect->order('person.last_name '.$sqlSort);
					break;
				case 'sortLocation':
					// currently no locations table; use locationId instead
					$dbSelect->order('clinicalNotes.locationId '.$sqlSort);
					break;
			}
			$maxResults = isset($custom['maxResults'])?$custom['maxResults']:'';
			if (strlen($maxResults) > 0) {
				$maxResultsValue = (int)$custom['maxResultsValue'];
				$dbSelect->limit($maxResultsValue);
			}
		}
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

	protected function _getDefaultSelect() {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			  ->from('clinicalNotes')
			  ->joinUsing('clinicalNoteDefinitions','clinicalNoteDefinitionId',array('noteTitle' => 'title'))
			  //->joinLeft('locations','locations.locationId = clinicalNotes.locationId', array('locationName' => 'name'))
			  ->joinLeft('person','person.person_id = clinicalNotes.authoringPersonId')
			  ->join('clinicalNoteTemplates','clinicalNoteDefinitions.clinicalNoteTemplateId = clinicalNoteTemplates.clinicalNoteTemplateId');
		return $dbSelect;
	}
}
