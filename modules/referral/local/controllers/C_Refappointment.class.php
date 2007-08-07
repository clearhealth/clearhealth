<?php
$loader->requireOnce('includes/DatasourceFileLoader.class.php');
$loader->requireOnce('includes/refEligibilitySchemaMapper.class.php');
class C_Refappointment extends Controller
{
	function actionAdd() {
		
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('clniConfirmLink', 'clniPopup', 'C_Refpractice');
		$ajax->stubs[] = 'C_Refpractice';
		
		$request =& Celini::newORDO('refRequest', $this->GET->get('refrequest_id'));
		$pprog = ORDataObject::factory('ParticipationProgram',$request->get('refprogram_id'));
		$this->sec_obj->acl_qcheck("edit",$this->_me,"",$pprog->get('participation_program_id'),$pprog,false);	
		$this->view->assign_by_ref('request', $request);
		
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('refProgramMembersWithSlots_DS');
		$possibleMembersDS =& new refProgramMembersWithSlots_DS($request);
		$possibleMembers =& new cGrid($possibleMembersDS);
		$possibleMembers->indexCol = false;
		$possibleMembers->pageSize = false;
		$possibleMembers->orderLinks = false;
		$this->view->assign_by_ref('possibleMembers', $possibleMembers);
		
		$program =& Celini::newORDO('refProgram', $request->get('refprogram_id'));
		if ($program->get('schema') == 0) {
			$this->view->assign('eligibilitySchema', 'Not Applicable');
		}
		else {
			$schemaMapper =& new refEligibilitySchemaMapper($program->get('schema'));
			$this->view->assign('eligibilitySchema', $schemaMapper->toList($request->get('eligibility')));
		}
		
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
		
		
		$this->view->assign('todaysMonth', date('n'));
		$this->view->assign('todaysDate', date('j'));
		$this->view->assign('todaysYear', date('Y'));

		// URL to change status - has the id appended to it in the template
		$this->assign('CHANGE_STATUS_URL', Celini::link('changestatus') . 'refRequest_id=' . $request->get('id') . '&process=true&refStatus=');
		$this->assign('SAVE_APPOINTMENT_ACTION', Celini::link('addAppointment'));

		$em =& Celini::enumManagerInstance();
		$this->assign('em',$em);
		

		return $this->view->render('add.html');
	}
	
	function processAddAppointment_add() {
		$appointment =& Celini::newORDO('refAppointment');
		$_POST['refAppointment']['date'] = $this->_buildTimestamp($_POST['refAppointment']);
		
		$appointment->populate_array($_POST['refAppointment']);
		$appointment->persist();
		
		$request =& Celini::newORDO('refRequest', (int)$appointment->get('refrequest_id'));
		$request->set('refappointment_id', $appointment->get('id'));
		
		$em =& Celini::enumManagerInstance();
		$request->set('refStatus', $em->lookupKey('refStatus', 'Appointment Pending'));
		$request->persist();
		
		Celini::redirectURL(Celini::link('view', 'Referral', true, $request->get('id')));
	}
	
	function process($args) {
		$id = isset($args['id']) ? (int)$args['id'] : 0;
		$args['date'] = $this->_buildTimestamp($args);
		$appointment =& Celini::newORDO('refAppointment', $id);
		$appointment->populate_array($args);
		$appointment->persist();
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
