<?php

$loader->requireOnce('includes/EnumManager.class.php');
$loader->requireOnce('includes/refEligibilitySchemaMapper.class.php');
$loader->requireOnce('includes/altPostOffice.class.php');
$loader->requireOnce('includes/refVisitList.class.php');
$loader->requireOnce('datasources/Person_ParticipationProgram_DS.class.php');

$loader->requireOnce('controllers/C_ReferralAttachment.class.php');
$loader->requireOnce('includes/clni/clniAudit.class.php');
$loader->requireOnce('ordo/PersonParticipationProgram.class.php');
$loader->requireOnce('datasources/refRequestList_DS.class.php');
$loader->requireOnce('datasources/refProgramList_DS.class.php');
$loader->requireOnce('datasources/FormDataByExternalByFormId_DS.class.php');

class C_Referral extends Controller
{
	var $_request = null;
	var $form_data_id = '';
	
	function C_Referral() {
		parent::Controller();
		$uploadAction = Celini::link('add', 'DocSmartStorable', false) . 'folder_id=1&tree_id=1';
		$this->assign('UPLOAD_ACTION', $uploadAction);
	}
	
	function list_action() {
		// Ideally, the Controller needs to know how to get at the various
		// file loaders.  Maybe Controller::getLoader('type')?
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('refRequestList_DS');
		$requestList =& new refRequestList_DS();
		
		$requestListGrid =& new cGrid($requestList);
		$requestListGrid->name = "formDataGrid";
		$requestListGrid->indexCol = false;

		// grab info need for the add referal stuff
		$dsLoader->load('refProgramList_DS');
		$programList =& new refProgramList_DS();
		$programList->clearFilters();
		$this->view->assign('programArray',$programList->toArray('refprogram_id','name'));

		
		$this->view->assign_by_ref('requestListGrid', $requestListGrid);
		return $this->view->fetch(Celini::getTemplatePath('/referral/' . $this->template_mod . '_list.html'));
	}
	
	function actionAdd($encounterId=0) {
		$patient_id = $this->get('patient_id', 'c_patient');
		$encounterId = (int)$encounterId;
		if ($patient_id <= 0) {
                        $this->messages->addMessage(
                                'No Patient Selected',
                                'Please select a patient before attempting to add an encounter.');
                        Celini::redirect('PatientFinder', 'List');
                }
		$patient = ORDataObject::factory('Person',$patient_id);
		$requestList =& new refRequestList_DS($patient_id);
		
		$requestListGrid =& new cGrid($requestList);
		$requestListGrid->name = "requestGrid";
		$requestListGrid->indexCol = false;
		$requestListGrid->prepare();
		
		// grab info need for the add referal stuff
		$me =& Me::getInstance();
		
		//get list of referral programs connected to patient
                $ppp = ORDataObject::factory('PersonParticipationProgram',$patient->get('person_id'));
		$conProgDS = new refProgramList_DS();
		$conProgDS->setQuery('cols',"pprog.participation_program_id, pprog.name as prog_name");
		$conProgDS->clearAll();
		$progList = $conProgDS->toArray("participation_program_id","prog_name");
                $this->view->assign('progNamesList',$progList);

		$this->view->assign('programArray', $progList);
		
		$this->view->assign('patient_id', $patient->get('id'));
		$this->view->assign('initiator_id', $me->get_person_id());
		
		// setup visit list
		$visitList =& new refVisitList($patient);
		$this->view->assign_by_ref('visitList', $visitList);

		$this->view->assign('FORM_ACTION', Celini::link('edit/0'));
		
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		//TODO fix permission, referal initator only, used in template
		$this->view->assign('canAdd', true);
		$this->view->assign("encounterId",$encounterId);
		if ($this->GET->exists('embedded')) {
			return $this->_minimalAdd();
		}
		else {
			$this->view->assign_by_ref('requestListGrid', $requestListGrid);
			
			return $this->_fullAdd();
		}
	}
	
	function _fullAdd() {
		return $this->view->render('add.html');
	}
	
	function _minimalAdd() {
		$this->view->assign('FORM_ACTION', Celini::link('edit/0', 'referral', 'main'));
		return $this->view->render('addMinimal.html');
	}
	
	function actionView($refRequest_id) {
		$this->view->assign('chlcare_base', isset($GLOBALS['config']['chlcare_base']) ? $GLOBALS['config']['chlcare_base'] : '');
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('clniConfirmLink', 'clniPopup');
		
		$request =& Celini::newORDO('refRequest', $refRequest_id);
		if ($request->get('patient_id') > 0) {
		$this->set('patient_id',$request->get('patient_id'),'c_patient');
		}
		if ($request->get('refRequest_id') > 0) {
			$this->set('requestId',$request->get('refRequest_id'));	
		}
		$this->_request =& $request;
		$this->view->assign_by_ref('request', $request);
		$program =& Celini::newORDO('refProgram', $request->get('refprogram_id'));
                $ppp = PersonParticipationProgram::getByProgramPatient($program->get('refprogram_id'),$request->get('patient_id'));
                //if patient doesn't already belong to program add them

                if (!$ppp->get('person_program_id') >0 ) {
                        $ppp->set('start',date('Y-m-d'));
                        $ppp->set('end',date('Y-m-d',strtotime ('today +1 year')));
                        $ppp->set('expires',0);
                        $ppp->set('active',1);
                        $ppp->set('person_id',$request->get('patient_id'));
                        $ppp->set('participation_program_id',$program->get('refprogram_id'));
                        $ppp->persist();
                }
                $this->assign("ppp",$ppp);
                $parProg = ORDataObject::factory('ParticipationProgram',$ppp->get('participation_program_id'));
                $optionsClassName = 'ParticipationProgram'. ucwords($parProg->get('class'));
                $GLOBALS['loader']->requireOnce('includes/ParticipationPrograms/'.$optionsClassName.".class.php");
                $options = ORDataObject::factory($optionsClassName, $ppp->get('person_program_id'));
		//if patient is eligible set request to request if current status is request/elig pending
		if ($options->get('eligibility') == 1 && $request->get('refStatus') == 2 && !$request->get('adhoc')) {
                       $request->set('refStatus', 1); //1 is requested
                       $request->persist();
                }
		elseif (($options->get('eligibility') == 2 || $options->get('eligibility') == 3) && $request->get('refStatus') == 1 && !$parProg->get('adhoc')) {
                       $request->set('refStatus', 2); //2 is elig pending
                       $request->persist();
		}
		elseif ($parProg->get('adhoc') == 1) {
                       $request->set('refStatus',1); //1 is requested
                       $request->persist();
		}
                $this->view->assign('options', $options);
                $this->view->assign_by_ref('refProgram', $program);
                $this->view->assign_by_ref('parProg', $parProg);
                $this->view->assign_by_ref('personParProgram', $ppp);
		$enc = ORDataObject::factory('Encounter',$request->get('visit_id'));
		$GLOBALS['loader']->requireOnce('datasources/Coding_List_DS.class.php');
                //true is to show only distinct codes, type 1 is CPT
                $cptDS = new Coding_List_DS($request->get('visit_id'),"1,3",true);
                $cptDS->clearLabels();
                $cptDS->setTypeDependentLabel("html","code","CPT");
                $cpts = implode(',',$cptDS->toArray("code"));
                $this->assign("cpts",$cpts);


                $icdDS = new Coding_List_DS($request->get('visit_id'),"2",true);
                $icdDS->clearLabels();
                $icdDS->setTypeDependentLabel("html","code","ICD");
                $icds = implode(",",$icdDS->toArray("code"));
                $this->assign("icds",$icds);
		$this->view->assign("enc",$enc);


		$requester =& Celini::newORDO('refUser',$request->get('initiator_id'),'ByExternalUserId');
		$this->view->assign_by_ref('requester',$requester);
		
		// log this opening 
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		$a = new clniAudit();
		$a->logOrdo($request, 'process', 'Opened by ' . $person->get('username'));
		
		
		// Must make $request think it's in persist mode due to some old, pre-value() code.
		$request->_inPersist = true;
		$mostRecentRequest =& Celini::newORDO(
			'refRequest', 
			array($request->get('patient_id'), $request->get('refSpecialty')),
			'MostRecentByPatientAndSpecialty');
		$request->_inPersist = false;
		$this->view->assign_by_ref('mostRecentRequest', $mostRecentRequest);
		if ($mostRecentRequest->isPopulated()) {
			$this->view->assign_by_ref('mostRecentRequestAppointment', $mostRecentRequest->getChild('refAppointment'));
		}
		
		$this->_addOccurence($request);
		$this->_setupEnums();
		
		$program =& Celini::newORDO('refProgram', $request->get('refprogram_id'));
		$this->view->assign_by_ref('refProgram', $program);

		$this->view->assign('FORM_ACTION', Celini::link('view/' . $request->get('id'), 'referral'));
		
		// url to attach files
		$this->view->assign('PRINT_URL', Celini::link('view/' . $request->get('id'), 'referral', 'minimal'));

		//$this->_initDocument($request);
		$this->assign_by_ref('initiator', $request->get('initiator'));
		
		//$this->_initPatientData($request->get('patient_id'));
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		
		//TODO fix permission, referral manager only
		//$this->view->assign('editReferralEligibility', $person->isType('Referral Manager', $request->get('refprogram_id')));
		$this->view->assign('editReferralEligibility', true);
		$this->view->assign('editTextFields', true);
		
		
		$em =& Celini::enumManagerInstance();
		$this->view->assign('em',$em);
		$session =& Celini::sessionInstance();
		$session->set('referral:currentProgramId', $request->get('refprogram_id'));
		
		return $this->_defaultView($request);
	}
	
	function processView($refRequest_id) {
		$cleanRefRequest = $this->POST->getTyped('refRequest', 'htmlsafe');
		$request =& Celini::newORDO('refRequest', $refRequest_id);
		$this->_request =& $request;
		$pprog = ORDataObject::factory('ParticipationProgram',$request->get('refprogram_id'));
		
		if (isset($cleanRefRequest['notes'])) {
			$this->checkPermission($pprog);
			$request->set('notes', $cleanRefRequest['notes']);
		}
		
                $request->persist();
	}
	function checkPermission($pprog) {
			if(Auth::canI('edit',$pprog->get('participation_program_id'))) {
			}
			elseif(Auth::canI('add',$pprog->get('participation_program_id'))) {
			}
			else {
			$this->sec_obj->acl_qcheck("edit",$this->_me,"",$pprog->get('participation_program_id'),$pprog,false);
			$this->sec_obj->acl_qcheck("add",$this->_me,"",$pprog->get('participation_program_id'),$pprog,false);
			}

	}
	
	function _appointmentPendingView(&$request) {
		// reset the refStatusList to only include appointment statuses
		$em =& Celini::enumManagerInstance();
		$refStatusList =& $em->enumList('refStatus');
		$refStatuses = $refStatusList->toArray();
		$refStatusLinks = array();
		foreach ($refStatuses as $key => $value) {
			if (!preg_match('/Appointment|Return/', $value)) {
				unset($refStatuses[$key]);
			}
			else {
				// create URL
				switch ($value) {
					case 'Appointment Kept' : 
						$refStatusLinks[$key] = Celini::link('view', 'refvisit') . 'refRequest_id=' . $request->get('id'). "&process=true";
						break;
					default :
						$refStatusLinks[$key] = Celini::link('changestatus', 'referral') . 'refRequest_id=' . $request->get('id') . '&process=true&refStatus=' . $key;
						break;
				}
			}
		}
		//var_dump($refStatusesLinks);
		
		$this->view->assign('refStatuses', $refStatuses);
		$this->view->assign('refStatusLinks', $refStatusLinks);
		
		$appointment =& Celini::newORDO('refAppointment', $request->get('refappointment_id'));
		$this->view->assign_by_ref('appointment', $appointment);
		return $this->view->render('viewAppointment.html');
	}
	
	function _appointmentKeptView(&$request) {
		// reset the refStatusList to only include appointment statuses
		$em =& Celini::enumManagerInstance();
		
		$appointment =& Celini::newORDO('refAppointment', $request->get('refappointment_id'));
		$this->view->assign_by_ref('appointment', $appointment);
		
		// CHLCare specific code
		$diagnoses =& Celini::newORDO('chlVisitDiagnosis', $request->get('refappointment_id'), 'ByVisit');
		$this->assign_by_ref('diagnoses', $diagnoses);
		
		return $this->view->render('appointmentKept.html');
	}

	
	function _defaultView($request) {
		$em =& Celini::enumManagerInstance();
		$this->view->assign('refRejectionReasons', $em->enumArray('refRejectionReason'));
		// URL to change status - has the id appended to it in the template
		$this->assign('CHANGE_STATUS_URL', Celini::link('changestatus', 'referral') . 'refRequest_id=' . $request->get('id') . '&process=true&refStatus=');
		
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		
		$refStatusList =& $em->enumList('refStatus');
		$refStatuses = $refStatusList->toArray();
		$refStatusLinks = array();
		foreach ($refStatuses as $key => $value) {
			// Managers can't fiddle with appointment statuses
			//todo fix permissions
			
			// create URL
			switch ($value) {
				case 'Appointment Kept' :
					$refStatusLinks[$key] = Celini::link('visit', 'Referral') . 'refRequest_id=' . $request->get('id'). "&process=true";
					break;
			default :
					$refStatusLinks[$key] = Celini::link('changestatus', 'referral') . 'refRequest_id=' . $request->get('id') . '&process=true&refStatus=' . $key;
					break;
					//$refStatusLinks[$key] = Celini::link('changestatus', 'referral') . 'refRequest_id=' . $request->get('id') . '&process=true&refStatus=' . $key;
			}
			$this->view->assign('refStatusLinks', $refStatusLinks);
		}
		
		$appointment =& Celini::newORDO('refAppointment', $request->get('refappointment_id'));
		$this->view->assign_by_ref('appointment', $appointment);
		// URL to add appointment
		//TODO fix permission
		//if ($person->isType('referral manager', $request->get('refprogram_id'))) {
		  if (true) {
			if (!$appointment->isPopulated() || $request->get('refStatus') == 7 || $request->value('refStatus') == 'Requested') {
				$this->assign('APPOINTMENT_BUTTON_URL', Celini::link('add', 'refappointment') . 'refrequest_id=' . $request->get('id'));
			}
			else {
				$this->view->assign('appointmentScheduled', true);
				$requestedStatus = $em->lookupKey('refStatus', 'Requested / Eligibility Pending');
				$this->assign('APPOINTMENT_BUTTON_URL', Celini::link('changeStatusCancel', 'referral') . 'refRequest_id=' . $request->get('id') . '&process=true&refStatus=' . $requestedStatus);
				//$this->assign('APPOINTMENT_FORM_ACTION', Celini::link('edit', 'refappointment') . 'refappointment_id=' . (int)$appointment->get('id') . '&embedded');
				
				// setup dates/years
				$dateArray = array();
				for ($i = 1; $i <= 31; $i++) {
					$dateArray[$i] = $i;
				}
				$this->view->assign('dateArray', $dateArray);
				
				$yearArray = array(date('Y', time()) => date('Y', time()));
				for ($i = 1; $i < 2; $i++) {
					$year = date('Y', strtotime('+' . $i . ' year'));
					$yearArray[$year] = $year;;
				}
				$this->view->assign('yearArray', $yearArray);
			}
		}
		
		$this->assign_by_ref('person', $person);

		$parProg = ORDataObject::factory("ParticipationProgram",$request->get('refprogram_id'));


		//appointment kept redirect to form
		if ($request->get('refStatus') == 5 || $parProg->get('adhoc') == 1) {
			return $this->actionVisit($request->get('refRequest_id'));
		}
		return $this->view->render('view.html');
	}
	
	function actionEdit($refRequest_id = 0) {
		
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		$em =& EnumManager::getInstance();
		$patientId = $this->GET->getTyped('patient_id', 'int');
		$this->assign('em',$em);
		
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('clniConfirmBox', 'clniPopup');
		
		$request =& Celini::newORDO('refRequest', $refRequest_id);
		$this->_request =& $request;
		if (!$request->isPopulated()) {
		$request->set('visit_id', $this->GET->getTyped('visit_id', 'int'));
		$request->set('refprogram_id', $this->GET->getTyped('program_id', 'int'));
		$request->set('patient_id', $patientId);
		}
		if ($patientId > 0){
		$this->set('patient_id',$patientId,'c_patient');
		}
		
		$this->view->assign_by_ref('request', $request);
		$this->_request = $request;
		$program =& Celini::newORDO('refProgram', $request->get('refprogram_id'));
		$ppp = PersonParticipationProgram::getByProgramPatient($program->get('refprogram_id'),$request->get('patient_id'));
		//if patient doesn't already belong to program add them
			
		if (!$ppp->get('person_program_id') >0 ) {
			$ppp->set('start',date('Y-m-d'));
			$ppp->set('end',date('Y-m-d',strtotime ('today +1 year')));
			$ppp->set('expires',0);
			$ppp->set('active',1);
			$ppp->set('person_id',$request->get('patient_id'));
			$ppp->set('participation_program_id',$program->get('refprogram_id'));
			$ppp->persist();
		}
		$this->assign("ppp",$ppp);
                $parProg = ORDataObject::factory('ParticipationProgram',$ppp->get('participation_program_id')); 
                $optionsClassName = 'ParticipationProgram'. ucwords($parProg->get('class'));
                $GLOBALS['loader']->requireOnce('includes/ParticipationPrograms/'.$optionsClassName.".class.php");
                $options = ORDataObject::factory($optionsClassName, $ppp->get('person_program_id'));
                $this->view->assign('options', $options);
		$this->view->assign_by_ref('refProgram', $program);
		$this->view->assign_by_ref('parProg', $parProg);
		$this->view->assign_by_ref('personParProgram', $ppp);
				
		$this->_addOccurence($request);
		$this->_setupEnums();
		
		$enc = ORDataObject::factory('Encounter',$request->get('visit_id'));
		$this->view->assign("enc",$enc);
		$GLOBALS['loader']->requireOnce('datasources/Coding_List_DS.class.php');
                //true is to show only distinct codes, type 1 is CPT
                $cptDS = new Coding_List_DS($request->get('visit_id'),"1,3",true);
                $cptDS->clearLabels();
                $cptDS->setTypeDependentLabel("html","code","CPT");
                $cpts = implode(',',$cptDS->toArray("code"));
                $this->assign("cpts",$cpts);


                $icdDS = new Coding_List_DS($request->get('visit_id'),"2",true);
                $icdDS->clearLabels();
                $icdDS->setTypeDependentLabel("html","code","ICD");
                $icds = implode(",",$icdDS->toArray("code"));
                $this->assign("icds",$icds);
		if ($request->get('refRequest_id') > 0) {
			$this->set('requestId',$request->get('refRequest_id'));	
		}
		$em =& EnumManager::getInstance();
		$this->view->assign("em", $em);
          
                $GLOBALS['loader']->requireOnce('includes/SpecialtyEnumByProgram.class.php');
                $enumGenerator =& new SpecialtyEnumByProgram($request->get('refprogram_id'));
                $this->view->assign('refSpecialty', $enumGenerator->toArray());

		return $this->view->render('edit.html');
	}
	
	function processChangeStatusCancel_edit() {
		$request =& Celini::newORDO('refRequest', $this->GET->getTyped('refRequest_id', 'int'));
		$this->_request =& $request;
		$pprog = ORDataObject::factory('ParticipationProgram',$request->get('refprogram_id'));
		$this->sec_obj->acl_qcheck("edit",$this->_me,"",$pprog->get('participation_program_id'),$pprog,false);
		$request->set('refStatus', $this->GET->get('refStatus'));
		$request->persist();
		$this->_state =false;
		return $this->actionView($this->GET->getTyped('refRequest_id', 'int'));
	}

	/**
	 * Process changing status on a request.
	 *
	 * All values should be handed in via _GET
	 *
	 * @access protected
	 */
	function processChangeStatus_edit() {
		$request =& Celini::newORDO('refRequest', $this->GET->getTyped('refRequest_id', 'int'));
		$this->_request =& $request;
		$pprog = ORDataObject::factory('ParticipationProgram',$request->get('refprogram_id'));
		$this->checkPermission($pprog);
		switch ($this->GET->get('refStatus')) {
			//can't change elig pending, request, app pending
			case 1:
			case 2:
			case 3:
			    $this->messages->addMessage("This status cannot be selected manually.");
			  break;
			///confirmed, kept, no-show initiator only
			case 4:
			  $this->checkPermission($pprog);
			  if (!$request->get('refappointment_id') > 0) {
			    $this->messages->addMessage("The Request must have an appointment to be set to the selected status.");
			    break;	
			  }
			  $request->set('refStatus', $this->GET->get('refStatus'));
			  break;
			//kept
			case 5:
			  if (!$request->get('refappointment_id') > 0) {
			    $this->messages->addMessage("The Request must have an appointment to be set to the selected status.");
			    break;	
			  }
			  else {
			
				$initiator = ORDataObject::factory('Person',$request->get('initiator_id'));
                		if ($initiator->get('primary_practice_id')>0) {
                        		if ($initiator->get('primary_practice_id') != $_SESSION['defaultpractice']) {
					$prac = ORDataObject::factory('Practice',$initiator->get('primary_practice_id')); echo $prac;
                                		$this->messages->addMessage('Your current practice selection must match the practice of this referral to edit it. ' . $prac->get('name'));
				$this->_state = false;
                        	return $this->fetch("main/general_message.html");
                     		}
                		}
			  }
			//no-show
			case 6:
			  $this->checkPermission($pprog);
			  if (!$request->get('refappointment_id') > 0) {
			    $this->messages->addMessage("The Request must have an appointment to be set to the selected status.");
			    break;	
			  }
			  $request->set('refStatus', $this->GET->get('refStatus'));
			  break;
			//returned
			case 7:
			  if ($request->get('refStatus') == 1 || $request->get('refStatus') == 2) {
			    $this->sec_obj->acl_qcheck("edit",$this->_me,"",$pprog->get('participation_program_id'),$pprog,false);
			    $request->set('refStatus', $this->GET->get('refStatus'));
			  }
			  else{
			    $this->messages->addMessage("Requests must be 'Requested' or 'Eligibility Pending' to be returned.");	
			  }
			  break;
			
		}
		$request->persist();
		
		// if rejected
		if ($this->GET->exists('reason') && $this->GET->get('refStatus') == 7) {
			$em =& Celini::enumManagerInstance();
			altPostOffice::sendORDONoticeToUser($request, 
				$request->get('initiator_id'), 
				array(
					'due_date' => date('Y-m-d'), 
					'note' => sprintf('<strong>Request %s rejected</strong><br /> %s',
						'<a target="_top" href="' . Celini::link('view/' . $request->get('id')) . '">' . $request->get('id') . '</a>',
						$em->lookup('refRejectionReason', $this->GET->getTyped('reason', 'int'))
					)
				)
			);
			$request->set('reason', $this->GET->getTyped('reason', 'int'));
			$request->persist();
		}
		$this->_state =false;
		return $this->actionView($this->GET->getTyped('refRequest_id', 'int'));
	}

	function processChangeNoACLStatus_edit() {
		$request =& Celini::newORDO('refRequest', $this->GET->getTyped('refRequest_id', 'int'));
		$this->_request =& $request;
		switch ($this->GET->get('refStatus')) {
			//can't change elig pending, request, app pending
			case 1:
			case 2:
			case 3:
			///confirmed, kept, no-show initiator only
			case 4:
			//kept
			case 5:
			//no-show
			case 6:
			//returned
			case 7:
			  $request->set('refStatus', $this->GET->get('refStatus'));
			  break;
			
		}
		$request->persist();
		//echo $request->get('refRequest_id'); 
		$this->_request = $request;
		
		$this->_state = false;
		return $this->actionView($this->GET->getTyped('refRequest_id', 'int'));
}

	function processVisit() {
		$requestId = (int)$_GET['refRequest_id'];
		$request = ORDataObject::factory("refRequest",$requestId);
		if (!$request->get('refappointment_id') > 0) {
                            $this->messages->addMessage("The Request must have an appointment to be set to the selected status.");
			  header('Location: ' . Celini::link('view','Referral',true,$requestId));
                            exit;
                }
		$parProg = ORDataObject::factory("ParticipationProgram",$request->get('refprogram_id'));
		$this->checkPermission($parProg);
		$this->_request = $request;
		$refvisit = ORDataObject::factory("refVisit");
		$refvisit->set('refreferral_visit_id',$request->get('refRequest_id'));
		$refvisit->set('refappointment_id',$request->get('refappointment_id'));
		$refvisit->persist();
	}
	function actionVisit($requestId = '', $formId = '') {
		$request = '';
		$requestId = (int)$requestId;
		$formId = (int)$formId;
		if (is_object($this->_request)) {
			$request = $this->_request;
		}
		else {
			$request = ORDataObject::factory("refRequest",$requestId);
		}
		//set to appointment kept status
		//$request->set('refStatus',5);
		$request->persist();
		if (empty($formId)) {
			$parProg = ORDataObject::factory("ParticipationProgram",$request->get('refprogram_id'));
			$formId = $parProg->get('form_id');
		}
		
		return $this->actionFillout($formId,$requestId);

	}
	function actionFillout($formId,$requestId) {
		$requestId = (int)$requestId;
		$request = ORDataObject::factory('refRequest',$requestId);
		$initiator = ORDataObject::factory('Person',$request->get('initiator_id'));
                	if ($initiator->get('primary_practice_id')>0) {
                        	if ($initiator->get('primary_practice_id') != $_SESSION['defaultpractice']) {
					$prac = ORDataObject::factory('Practice',$initiator->get('primary_practice_id'));
                                                $this->messages->addMessage('Your current practice selection must match the practice of this referral to edit it. ' . $prac->get('name'));
                        	return $this->fetch("main/general_message.html");
                     		}
                	}

		$formId = (int)$formId;
                $formDataId = $this->_getFormDataId('participation',$formId);
		
		$GLOBALS['loader']->requireOnce("controllers/C_Form.class.php");
		$fc = new C_Form();

		$enc = ORDataObject::factory('Encounter',$request->get('visit_id'));
		$fc->view->assign("enc",$enc);

		$perParProg = PersonParticipationProgram::getByProgramPatient($request->get('refprogram_id'),$request->get('patient_id'));
		$parProg = ORDataObject::factory('ParticipationProgram',$perParProg->get('participation_program_id'));
		$fc->view->assign("parProg",$parProg);

		$fd = ORDataObject::factory("FormData",$formDataId);
		if (!$fd->isPopulated()) {
		$fd->set("form_id",$formId);
		$fd->set("external_id",$request->get('refRequest_id'));
		$fd->set("last_edit",date('Y-m-d H:i:s'));
		$fd->persist();
		}

		$fc->view->assign("request",$request);

		$GLOBALS['loader']->requireOnce("controllers/C_Coding.class.php");
                $cc = new C_Coding();
		//put coding block section in ajax submit mode
		$cc->assign("ajaxSubmit",true);
		$codingBlock = $cc->update_action_edit($request->get('refRequest_id'));
		$fc->assign('codingBlock',$codingBlock);

		$GLOBALS['loader']->requireOnce('datasources/Coding_List_DS.class.php');
		//true is to show only distinct codes, type 1 is CPT
		$cptDS = new Coding_List_DS($request->get('visit_id'),"1,3",true);
		$cptDS->clearLabels();
		$cptDS->setTypeDependentLabel("html","code","CPT");
		$cpts = implode(',',$cptDS->toArray("code"));
		$fc->assign("cpts",$cpts);
		
		$icdDS = new Coding_List_DS($request->get('visit_id'),"2",true);
		$icdDS->clearLabels();
		$icdDS->setTypeDependentLabel("html","code","ICD");
		$icds = implode(",",$icdDS->toArray("code"));
		$fc->assign("icds",$icds);
		$fc->view->assign('patientId',$this->get('patient_id','c_patient'));
		if ($request->get('refappointment_id') > 0) {
		$appointment =& Celini::newORDO('refAppointment', $request->get('refappointment_id'));
		$fc->assign('appointment', $appointment);
		}

                return $fc->actionFillout_edit($formId,$fd->get('form_data_id'));
	}
	function processFillout_edit($formId) {
                $formId = (int)$_POST['form_id'];
                $formDataId = $this->_getFormDataId($_POST['filloutType'],$formId);
		
                $this->form_data_id = $formDataId;
                $this->formId = $formId;
                $GLOBALS['loader']->requireOnce("controllers/C_Form.class.php");
                $form_controller = new C_Form();
                $form_controller->setExternalId($this->get('requestId'));
                $form_controller->processFillout_edit($formId, $this->form_data_id);
                $this->form_data_id = $form_controller->form_data_id;
        }

	function processEdit($refRequest_id = 0) {
                $request = ORDataObject::factory('refRequest', $refRequest_id);
                $this->_request = $request;
                $request->populateArray($_POST['refRequest']);
		//var_dump($_POST);exit;
                $request->set('refStatus', 2);
		$me =& Me::getInstance();
		$request->set('initiator_id', $me->get_person_id());
//		echo $this->_me->get_person_id();exit;
                $parProg = ORDataObject::factory('ParticipationProgram', $request->get("refprogram_id"));
		//permissions apply only to non-adhoc programs
		if ($parProg->get('adhoc') == 0) {
			$this->checkPermission($parProg);
		}
                $request->persist();
		$ppp = PersonParticipationProgram::getByProgramPatient($request->get('refprogram_id'),$request->get('patient_id'));
		$optionsClassName = 'ParticipationProgram'. ucwords($parProg->get('class'));
                $GLOBALS['loader']->requireOnce('includes/ParticipationPrograms/'.$optionsClassName.".class.php");
                $options = ORDataObject::factory($optionsClassName, $ppp->get('person_program_id'));
		//eligibility == 1 is eligible
		if ($options->get('eligibility') == 1 || $request->get('adhoc')) {
                	$request->set('refStatus', 1); //1 is requested
                	$request->persist();
		}
		$this->_continue_processing = false;
		if ($request->get('refRequest_id') > 0) {
			$this->set('requestId',$request->get('refRequest_id'));	
		}
		if ($parProg->get('adhoc') == 1) {
			header('Location: ' . Celini::link('fillout') . "formId=" . $parProg->get('form_id') . "&requestId=" . $request->get('id'));
		exit;
		}
		header('Location: ' . Celini::link('view/' . $request->get('id')) );
		exit;

        }

	function _addOccurence(&$request) {
		if ($request->get('visit_id') > 0) {
			global $loader;
			$loader->requireOnce('controllers/C_Refvisit.class.php');
			$visitController =& new C_Refvisit();
			$this->view->assign('visitInfo', $visitController->actionCHLVisit($request->get('visit_id'),$request));
			
			$visit =& Celini::newORDO('refVisit', $request->get('visit_id'));
		}
		else {
			$this->view->assign('visitInfo', false);
		}
	}
	
	function _setupEnums() {
		$em =& EnumManager::getInstance();
		
		$GLOBALS['loader']->requireOnce('includes/SpecialtyEnumByProgram.class.php');
		$enumGenerator =& new SpecialtyEnumByProgram($this->_request->get('refprogram_id'));
		$this->view->assign('refSpecialty', $enumGenerator->toArray());
		
		$this->view->assign('refEligibility', $em->enumArray('refEligibility'));
		
		$this->view->assign('refRequested_day', $em->enumArray('days'));
		$this->view->assign('refRequested_time', $em->enumArray('refRequested_time'));
		$this->view->assign('yesNoArray', $em->enumArray('yesNo'));
		
		$refStatusList =& $em->enumList('refStatus');
		$this->view->assign('refStatuses', $refStatusList->toArray());
	}

	function actionSummary() {
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('refRequestList_DS');
		$requestList =& new refRequestList_DS();
		
		$requestListGrid =& new cGrid($requestList);
		$requestListGrid->name = "formDataGrid";
		$requestListGrid->indexCol = false;
		$this->view->assign_by_ref('requestListGrid', $requestListGrid);
		return $this->view->fetch(Celini::getTemplatePath('/referral/' . $this->template_mod . '_summary.html'));
	}
	function _getFormDataId($filloutType = 'participation',$formId) {
                $formDataId = 0;
                switch($filloutType) {
                  case 'participation':
                        $fdDS =  new FormDataByExternalByFormId_DS($this->get('requestId'),$formId);
                        $fdDS->rewind();
                        $row = $fdDS->get();

                        if (is_array($row)) {
                                $formDataId = $row['form_data_id'];
                        }
                }
                return $formDataId;
        }
	function _buildTimestamp($array) {
                $dateString =
                        $array['date_year'] . '-' .
                        $array['date_month'] . '-' .
                        $array['date_date'];
                $timeObj = &TimeObject::create12Hour(
                        $array['time_digits'] . ' ' .
                        $array['time_suffix']);
                return $dateString . ' ' . $timeObj->toString('%H:%i:%s');
        }
	
}

