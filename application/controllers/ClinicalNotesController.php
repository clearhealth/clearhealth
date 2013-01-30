<?php
/*****************************************************************************
*       ClinicalNotesController.php
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


class ClinicalNotesController extends WebVista_Controller_Action {

	protected $_visit;
	protected $_patient;
	protected $_location;
        protected $_form;

        public function init() {
                $this->_session = new Zend_Session_Namespace(__CLASS__);
                $mainc = new Zend_Session_Namespace('MainController');
                $this->_patient = $mainc->patient;
                $this->_visit = $mainc->visit;
                $this->_location = $mainc->location;
        }

	public function processEditAnnotationAction() {
		$params = $this->_getParam('annotation');
		$cnAnnotation = new ClinicalNoteAnnotation();
		$cnAnnotation->populateWithArray($params);
		$cnAnnotation->persist();
		$msg = __("Record saved successfully");
		$data = array();
		$data['msg'] = $msg;
		$data['clinicalNoteAnnotationId'] = $cnAnnotation->clinicalNoteAnnotationId;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAnnotationAction() {
		$clinicalNoteAnnotationId = (int)$this->_getParam('clinicalNoteAnnotationId');
		$cnAnnotation = new ClinicalNoteAnnotation();
		$cnAnnotation->clinicalNoteAnnotationId = $clinicalNoteAnnotationId;
		$cnAnnotation->setPersistMode(WebVista_Model_ORM::DELETE);
		$cnAnnotation->persist();
		$msg = __("Record deleted successfully");
		$data = array();
		$data['code'] = 200;
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Create unsigned note by XML data
	 */
	function addXmlNoteAction() {
		/*
		XML Data structure?
		<clinicalNotes>
			<note>
				<personId>1234</personId>
				<authoringPersonId>5678</authoringPersonId>
				<dateTime>2009-07-01</dateTime>
				<clinicalNoteDefinitionId>20090701</clinicalNoteDefinitionId>
				<clinicalNoteDefinitionId>20090702</clinicalNoteDefinitionId>
				<clinicalNoteDefinitionId>20090703</clinicalNoteDefinitionId>
			</note>
		</clinicalNotes>
		*/
		$xmlData = (int)$this->_getParam('xmlData');

		$xml = simplexml_load_string($xmlData);
		if ($xml === false) {
			// error or malformed xml
			$msg = "Error or malformed XML data.";
			throw new Exception($msg);
		}
		$clinicalNoteId = array();
		foreach ($xml->note as $note) {
			$personId = $note->personId;
			$cnParams = array();
			$cnParams['authoringPersonId'] = (string)$note->authoringPersonId;
			$cnParams['dateTime'] = (string)$note->dateTime;
			$clinicalNote = new ClinicalNote();
	                $clinicalNote->populateWithArray($cnParams);
			$clinicalNote->personId = $personId;
			foreach ($note->clinicalNoteDefinitionId as $cndId) {
				// add note one by one
				$clinicalNote->clinicalNoteId = 0;
				$clinicalNote->clinicalNoteDefinitionId = (string)$cndId;
				$clinicalNote->persist();
			}
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$data = array();
		$data['msg'] = __('Data saved.');
                $json->direct($data);
	}

	public static function buildJSJumpLink($objectId,$signingUserId,$objectClass) {
		$objectClass = 'Notes'; // temporarily hard code objectClass based on MainController::getMainTabs() definitions
		$clinicalNote = new ClinicalNote();
		$clinicalNote->clinicalNoteId = $objectId;
		$clinicalNote->populate();
		$patientId = $clinicalNote->personId;

		$js = parent::buildJSJumpLink($objectId,$patientId,$objectClass);
		$js .= <<<EOL

mainTabbar.setOnTabContentLoaded(function(tabId){
	TabState.setParam({'filter':'byLast100','personId':patientId});
	TabState.redrawTab(objectId);
	loadTemplatePane(objectId);
});

EOL;
		return $js;
	}

	public function indexAction() {
		$identity = Zend_Auth::getInstance()->getIdentity();
		$personId = (int)$identity->personId;

		$userId = $identity->userId;
		$user = new User();
		$user->userId = $userId;
		$user->populate();
		$currentPractice = '';
		$currentBuilding = '';
		if (strlen($user->preferences) > 0) {
			$this->xmlPreferences = new SimpleXMLElement($user->preferences);
			$currentLocation = (string)$this->xmlPreferences->currentLocation;
			$locationId = (int)$currentLocation;
			if ($locationId > 0) {
				$currentLocation = Room::location($locationId,true);
				$currentPractice = $currentLocation['practice'];
				$currentBuilding = $currentLocation['practice'].'->'.$currentLocation['building'];
			}
		}
		$this->view->currentPractice = $currentPractice;
		$this->view->currentBuilding = $currentBuilding;

		$this->view->currentPersonId = $personId;
		$provider = new Provider();
		$provider->person_id = $personId;
		$provider->populate();
		$this->view->provider = $provider;
		$this->render();
	}

	public function toolbarXmlAction() {
		$this->view->xmlHeader = '<?xml version=\'1.0\' encoding=\'iso-8859-1\'?>' . "\n";
		header('content-type: text/xml');
		$this->render();
	}

	function addNoteAction() {
		$personId = (int)$this->_getParam('personId');
		$this->view->personId = $personId;
		$this->view->clinicalNoteDefinitions = new ClinicalNoteDefinitionIterator();
		$this->view->currentPersonId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$this->view->personId = $personId;

		$form = new WebVista_Form(array('name' => 'add-note'));
		$form->setAction(Zend_Registry::get('baseUrl') . "clinical-notes.raw/process-add-note");
		$clinicalNote = new ClinicalNote();
		$form->loadORM($clinicalNote, "ClinicalNote");
		$form->setWindow('windowAddNote');
		$this->view->form = $form;
        }

	function deleteNoteAction() {
		$clinicalNoteId = $this->_getParam('clinicalNoteId');
                $db = Zend_Registry::get('dbAdapter');
		$signatureIterator = new ESignatureIterator();
		$signatureIterator->setFilter($clinicalNoteId);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$clinicalNote = new ClinicalNote();
		$clinicalNote->clinicalNoteId = (int)$clinicalNoteId;
		if (!$clinicalNote->populate()) {
			// just remove the revision history
			$db->delete("genericData", "objectClass = 'ClinicalNote' and revisionId = " . (int)$clinicalNoteId);
			$json->direct(true);
			return;
		}
		if (!$signatureIterator->valid()) {
			$db->beginTransaction();
			try {
				$db->delete("eSignatures", "objectClass = 'ClinicalNote' and objectId = " . (int)$clinicalNoteId); // TODO: objectId refers to genericData.revisionId
				$db->delete("clinicalNotes", "clinicalNoteId = " . (int)$clinicalNoteId);
				$db->delete("genericData", "objectClass = 'ClinicalNote' and objectId = " . (int)$clinicalNoteId);
				$db->commit();
			}
			catch (Exception $e) {
				$db->rollBack();
				$this->getResponse()->setHttpResponseCode(500);
				$json->direct(array('error' => $e->getMessage()));
				return;
			}
			$json->direct(true);
			return;
		}
		$this->getResponse()->setHttpResponseCode(500);
		$json->direct(array('error' => "You cannot delete a note which has previously been signed."));
	}

	function processAddNoteAction() {
		$personId = 0;
		$personId = (int)$this->_getParam('personId');
                $cnParams = $this->_getParam('clinicalNote');
		$clinicalNoteDefinitionId = $cnParams['clinicalNoteDefinitionId'];
		unset($cnParams['clinicalNoteDefinitionId']);
		$clinicalNote = new ClinicalNote();
                $clinicalNote->populateWithArray($cnParams);
		$noteLocationId = (int)$clinicalNote->locationId;
		if (!$noteLocationId > 0) {
			$identity = Zend_Auth::getInstance()->getIdentity();
			$user = new User();
			$user->userId = (int)$identity->userId;
			$user->populate();
			$preferences = $user->xmlPreferences;
			if ($preferences !== null) {
				$noteLocationId = (int)$preferences->currentLocation;
			}
		}
		$clinicalNote->locationId = $noteLocationId;
		$clinicalNote->personId = $personId;
		//$clinicalNote->visitId = $this->_visit->visitId;
		//$clinicalNote->locationId = $this->_location->locationId;
		foreach ($clinicalNoteDefinitionId as $cndId) {
			// add note one by one
			$clinicalNote->clinicalNoteId = 0;
			$clinicalNote->clinicalNoteDefinitionId = $cndId;
			$clinicalNote->persist();
		}
		//var_dump($_POST);exit;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array('clinicalNoteId' => $clinicalNote->clinicalNoteId));
	}

	public function processEditNoteAction() {
		$ret = true;
		$clinicalNote = new ClinicalNote();
		$clinicalNote->clinicalNoteId = (int)$this->_getParam('clinicalNoteId');
		$clinicalNote->populate();
		if ($clinicalNote->eSignatureId > 0) {
			$clinicalNote->eSignatureId = 0;
			$revisionId = (int)$this->_getParam('revisionId');
			GenericData::createRevision(get_class($clinicalNote),$clinicalNote->clinicalNoteId,$revisionId);
			$clinicalNote->persist();
		}
		else {
			$ret = __('Selected note must be signed first.');
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	function listNotesAction() {
		$personId = 0;
		$personId = (int)$this->_getParam('personId');
		$filter = $this->_getParam('filter');
		$clinicalNoteIterator = new ClinicalNoteIterator();
		$custom = $this->_getParam('custom');
		if (count($custom) > 0) {
			$custom = $this->_getParam('custom');
			$custom['personId'] = $personId;
			$clinicalNoteIterator->customView($custom);
		}
		else {
			$data = array();
			$data['personId'] = $personId;
			if ($filter == 'byAuthoringPersonId') {
				$data['authoringPersonId'] = $this->_getParam('authoringPersonId');
			}
			if ($filter == 'byDateRange') {
				$data['dateRange'] = $this->_getParam('dateRange');
			}
			if ($filter == 'byCurrentPractice' || $filter == 'bySelectedVisit') {
				$visitId = (int)$this->_getParam('visitId');
				$visit = new Visit();
				$visit->visitId = $visitId;
				$visit->populate();

				$building = new Building();
				$building->buildingId = $visit->buildingId;
				$building->populate();

				$data['locationId'] = $building->buildingId;
				$data['selectedVisit'] = date('Y-m-d',strtotime($visit->timestamp));
			}
			if ($filter == 'byVisitPractice' || $filter == 'byVisitBuilding') {
				$practiceId = 0;
				$buildingId = 0;
				$identity = Zend_Auth::getInstance()->getIdentity();
				$userId = $identity->userId;
				$user = new User();
				$user->userId = $userId;
				$user->populate();
				if (strlen($user->preferences) > 0) {
					$this->xmlPreferences = new SimpleXMLElement($user->preferences);
					$currentLocation = (string)$this->xmlPreferences->currentLocation;
					$locationId = (int)$currentLocation;
					if ($locationId > 0) {
						$room = new Room();
						$room->roomId = $locationId;
						$room->populate();
						$practiceId = $room->building->practiceId;
						$buildingId = $room->buildingId;
					}
				}
				$data['practiceId'] = $practiceId;
				$data['buildingId'] = $buildingId;
			}
			$clinicalNoteIterator->setFilter($filter,$data);
		}

                //trigger_error($cnSelect->__toString(),E_USER_NOTICE);
                //var_dump($db->query($cnSelect)->fetchAll());exit;
		$notes = array();
                foreach($clinicalNoteIterator as $note) {

			if ($note['eSignatureId'] > 0) {
				$img = 'sm-signed.png';
				$alt = 'Signed';
			}
			else {
				$img = 'sm-editproblem.png';
				$alt = 'Editing';
			}
			$icon = '<img src="'.Zend_Registry::get('baseUrl').'img/'.$img.'" alt="'.$alt.'" />';

                        $row = array();
                        $row['id'] = $note['clinicalNoteId'];
                        $row['data'][] = '';
                        $row['data'][] = $icon.' '.date('Y-m-d',strtotime($note['dateTime']));
                        $row['data'][] = $note['noteTitle'];
                        $row['data'][] = $note['last_name'].', '.$note['first_name'].' '.substr($note['middle_name'],0,1);
			$location = '';
			$locationId = (int)$note['locationId'];
			if ($locationId > 0) {
				$location = Room::location($locationId);
			}
			$row['data'][] = $location;
			$row['data'][] = $row['id'];
			$row['data'][] = 0;

			$xml = simplexml_load_string($note['template']);
			$genericData = new GenericData();
			$filters = array();
			$filters['objectId'] = $note['clinicalNoteId'];
			$filters['objectClass'] = 'ClinicalNote';
			$genericDataIterator = $genericData->getIteratorByFilters($filters);
			$firstData = false;
			$childIcon = '<img src="'.Zend_Registry::get('baseUrl').'img/leaf.gif" alt="-" />';
			foreach ($genericDataIterator as $data) {
				if (!$firstData) {
					$firstData = true;
					//$row['id'] .= ':'.$data->revisionId;
					$row['data'][6] = $data->revisionId;
					continue;
				}
				$tmp = array();
				$tmp['id'] = $data->revisionId;
               	        	$tmp['data'][] = $data->objectId; // '';
				$tmp['data'][] = '&nbsp; &nbsp; &nbsp;'.$childIcon.' '.date('Y-m-d',strtotime($data->dateTime));
				$tmp['data'][] = $note['noteTitle'];
				$tmp['data'][] = ''; // Author
				$tmp['data'][] = ''; // Location
				$tmp['data'][] = $note['clinicalNoteId'];
				$tmp['data'][] = $tmp['id'];
				$row['rows'][] = $tmp;
			}
			$notes[] = $row;
                }

		$acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $acj->suppressExit = true;
                $acj->direct(array("rows" => $notes));
	}

	public function clinicalNotesGridContextMenuAction() {
                header('Content-Type: application/xml;');
                $this->view->xmlHeader = '<?xml version="1.0" ?>';
                $this->render();
        }

	/*
	 * Used in popup window to filter notes by author
	 */
	public function notesByAuthorAction() {
		$this->render();
	}

	/*
	 * Used in popup window to filter notes by date range
	 */
	public function notesByDateRangeAction() {
		$this->render();
	}

	/*
	 * Used in popup window to filter notes by custom view
	 */
	public function notesByCustomViewAction() {
		$this->render();
	}

	public function getVisitInfoAction() {
		$visitId = (int)$this->_getParam('visitId');
		$visit = new Visit();
		$visit->visitId = $visitId;
		$visit->populate();

		$practice = new Practice();
		$practice->practiceId = $visit->practiceId;
		$practice->populate();

		$data = array();
		$data['currentPractice'] = $practice->name.'';
		$data['selectedVisit'] = substr($visit->dateOfTreatment,0,10).'';

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
