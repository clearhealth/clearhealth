<?php
$loader->requireOnce('controllers/C_Chllabtests.class.php');
$loader->requireOnce('includes/DatasourceFileLoader.class.php');
$loader->requireOnce('includes/chlUtility.class.php');
$loader->requireOnce('ordo/CodingData.class.php');

/**
 * @todo abstract out CHLCare code so it only is called in instances where the
 *    referral system is being run in CHLCare.
 */
class C_Refvisit extends Controller
{
	function actionCHLVisit($visit_id,$request=false) {
		$visit =& Celini::newORDO('refVisit', (int)$visit_id);
		// double check in case we have a bad $visit_id
		if (!$visit->isPopulated()) {
			return '';
		}
		if($request !== false) {
			if($request->get('id') < 1) {
				$refUser =& Celini::newORDO('refUser',$this->_me->get_user_id(),'ByExternalUserId');
			} else {
				$refUser =& Celini::newORDO('refUser',$request->get('initiator_id'),'ByExternalUserId');
			}
			$this->view->assign('visitLocation', $refUser->value('clinicName'));
		}
		$cd = ORDataObject::factory('CodingData');
		$pCodeList = $cd->getCodeList($visit_id);
		$codeDisplay = array();
		foreach ($pCodeList as $pCode) {
			$childrenAr = $cd->getChildCodes($pCode['coding_data_id']);
			$cCodeAr = array();
			foreach ($childrenAr as $cCode) {
				$cCodeAr[] = $cCode['code'];
			}
			$codeDisplay[] = array ('pcode' => $pCode['code'], 'diags' => implode(', ',$cCodeAr));

		}
		$this->view->assign_by_ref('codeList', $codeDisplay);
		$this->view->assign_by_ref('visit', $visit);
		return $this->view->render('chlvisit.html');
	}
	
	
	/**
	 * The default VDR page
	 *
	 * @todo Pull CHLCare specific code out
	 */
	function actionView() {
		$request =& Celini::newORDO('refRequest', $this->GET->getTyped('refRequest_id', 'int'));
		$this->view->assign_by_ref('request', $request);
		// save association between visit and request
		
		// make sure the right value is set
		$em =& Celini::enumManagerInstance();
		$request->set('refStatus', $em->lookupKey('refStatus', 'Appointment Kept'));
		$request->persist();
		
		/*$GLOBALS['loader']->requireOnce('includes/chlDiagnosisAndProcedures.class.php');
		$diagDisplay =& new chlDiagnosisAndProcedures();
		$diagDisplay->visitId = $request->get('refappointment_id');
		$this->view->assign('diagnosisDisplay', $diagDisplay->display());*/
		
		if ($request->get('visit_id') > 0) {
			$GLOBALS['loader']->requireOnce('controllers/C_Refvisit.class.php');
			$visitController =& new C_Refvisit();
			$this->view->assign('visitInfo', $this->actionCHLVisit($request->get('visit_id')));

			$visit =& Celini::newORDO('refVisit', $request->get('visit_id'));
			$this->view->assign('visitLocation', $visit->get('clinic_name'));
		}

		$appointment =& Celini::newORDO('refAppointment', $request->get('refappointment_id'));
		$this->view->assign_by_ref('appointment', $appointment);
		
		$practice =& Celini::newORDO('refPractice', $appointment->get('refpractice_id'));
		$this->view->assign_by_ref('practice', $practice);
		
		$location =& Celini::newORDO('refPracticeLocation', $appointment->get('reflocation_id'));
		$this->view->assign_by_ref('location', $location);
		
		$provider =& Celini::newORDO('refProvider', $appointment->get('refprovider_id'));
		$this->view->assign_by_ref('provider', $provider);
		
		$this->view->assign('specialties', implode("\n", $practice->get('specialties')));
		
		// bring in lab tests
	/*	$labTestController =& new C_Chllabtests();
		$labTestController->embedded = true;
		$labTestController->GET->set('visit_id', $appointment->get('id'));
		$this->view->assign('labTestDisplay', $labTestController->actionAdd());*/
		/*
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('chlLabResultsByVisit_DS');
		$labResults =& new chlLabResultsByVisit_DS($appointment->get('id'));
		$existingLabResults =& new cGrid($labResults);
		$existingLabResults->indexCol = false;
		$existingLabResults->prepare();
		$this->view->assign_by_ref('existingLabResults', $existingLabResults);
		*/
		$program =& $provider->getParent('refProgram');
		$this->view->assign_by_ref('refProgram',$program);
		
		$this->view->assign('FORM_ACTION', Celini::link('addReferral', 'refvisit'));
		
		// load patient context menu
		return $this->view->render('view.html');
	}
	
	function processAddReferral_add() {
		//printf('<pre>%s</pre>', var_export($_POST , true));exit;
		
		$em =& Celini::enumManagerInstance();		
		
		// Save diagnosis data for CHLCare
		$diagnosis =& Celini::newORDO('chlVisitDiagnosis');
		$diagnosis->set('visit_type', 'referral');
		$diagnosis->set('visit_id', $request->get('refappointment_id'));
		$diagnosis->set('patient_id', $request->get('patient_id'));
		if (isset($_POST['chlVisitDiagnosis']) && count($_POST['chlVisitDiagnosis']) > 0) {
			$diagnosis->set('diagnoses', ':|:' . implode(':|:', $_POST['chlVisitDiagnosis']));
		}
		if (isset($_POST['chlVisitProcedure']) && count($_POST['chlVisitProcedure']) > 0) {
			$diagnosis->set('procedures', ':|:' . implode(':|:', $_POST['chlVisitProcedure']));
		}
		$diagnosis->persist();
		
		header('Location:' . Celini::link('addreferraltwo/') . 'refrequest_id=' .  $request->get('id'));
		exit;
	}
	
		
	function actionAddReferralTwo_add() {
		// bring in lab tests
		$request =& Celini::newORDO('refRequest', $this->GET->get('refrequest_id'));
		$labTestController =& new C_Chllabtests();
		$labTestController->embedded = true;
		$labTestController->GET->set('visit_id',  $request->get('refappointment_id'));
		$this->view->assign('labTestDisplay', $labTestController->actionAdd());

		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('chlLabResultsByVisit_DS');
		$labResults =& new chlLabResultsByVisit_DS($request->get('refappointment_id'));
		$existingLabResults =& new cGrid($labResults);
		$existingLabResults->indexCol = false;
		$existingLabResults->prepare();
		$this->view->assign_by_ref('existingLabResults', $existingLabResults);
		
		$this->view->assign('requestUrl', Celini::link('view/' . $request->get('id'), 'referral'));
		return $this->view->render('stepTwo.html');	
	}
}

