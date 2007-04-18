<?php
$loader->requireOnce('includes/acl/Auth.class.php');
$loader->requireOnce('includes/acl/CeliniACL.class.php');
$loader->requireOnce('controllers/Controller.class.php');
$loader->requireOnce('includes/EnumManager.class.php');
$loader->requireOnce('includes/refSpecialtyMapper.class.php');
$loader->requireOnce('datasources/ParticipationProgram_DS.class.php');
class C_Refprogram extends Controller
{
	/**
	 * A temporary value for passing ID's between methods
	 *
	 * @var int
	 * @access private
	 */
	var $_refprogram_id = 0;
	

	/**
	 * Returns a list of programs.
	 */
	function actionList() {
		
		
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('refProgramList_DS');
		$programList =& new refProgramList_DS();
		//todo fix permission
		//$programList->showMemberProgramsOnly();
		
		$programListGrid =& new cGrid($programList);
		$programListGrid->name = "refProgramListDSGrid";
		$programListGrid->indexCol = false;
		
		$this->view->assign('addProgramURL', Celini::link('edit'));
		$this->view->assign_by_ref('programListGrid', $programListGrid);
		return $this->view->fetch(Celini::getTemplatePath('/refprogram/' . $this->template_mod . '_list.html'));
	}
	
	
	/**
	 * Returns a program in edit mode
	 */
	function actionEdit($refprogram_id = 0) {
		$me =& Me::getInstance();
		$person =& Celini::newORDO('Person', $me->get_person_id());
		//TODO permission for program administration	
		if ($refprogram_id == 0 && $this->_refprogram_id > 0) {
			$refprogram_id = $this->_refprogram_id;
		}
		
		$refProgram = Celini::newORDO('refProgram', $refprogram_id);
		$this->view->assign_by_ref('program', $refProgram);
		

		$conProgDS = new ParticipationProgram_DS();
                $conProgDS->setQuery('cols',"pprog.participation_program_id, pprog.name as prog_name");
                $conProgDS->clearAll();
                $progList = $conProgDS->toArray("participation_program_id","prog_name");
                $this->view->assign('progNamesList',$progList);


		$programMemberCollection =& $refProgram->getChildren('members');
		$programMemberArray = $programMemberCollection->toArray();
		if (count($programMemberArray) > 0) {
			$this->view->assign('programMemberArray', $programMemberArray);
		}
		else {
			$this->view->assign('programMemberArray', false);
		}
		
		$displayMonths = array();
		for ($i = 0; $i < 12; $i++) {
			$displayMonths[] = date('M y', strtotime($i . ' month'));
		}
		$this->view->assign('displayMonths', $displayMonths);
		
		$refProvider =& Celini::newORDO('refProvider');
		$this->view->assign('availableProviders', $refProvider->keyNameArray());
		
		$refPractice =& Celini::newORDO('refPractice');
		$this->view->assign('availablePractices', $refPractice->keyNameArray());

		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'refProgramMemberSlot';

		$em =& EnumManager::getInstance();
		$this->assign('schemaList', $em->enumArray('refEligibilitySchema'));
		
		$roleTypeList = $em->enumArray('refUserType');
		//TODO fix permission here, clinic admin== ! referral manager
		$this->assign('roleTypeList', $roleTypeList);
		
		// need to provide a way to configure what class/call-back will get supply this info
		global $loader;
		$loader->requireOnce('includes/chlProviderArray.class.php');
		$providerArrayObj =& new chlProviderArray();
		$this->view->assign_by_ref('providerArrayObj', $providerArrayObj);
		
		// list of users
		$userDS =& $refProgram->loadDatasource('refUserList');
		//TODO fix permission
		//$userDS->isAdmin = ($person->isType('super admin') || $person->isType('Clinic Administrator'));
		
		$userGrid =& new cGrid($userDS);
		$userGrid->indexCol = false;
		$this->view->assign_by_ref('userGrid', $userGrid);
		
		// list of attached practices
		$practiceGrid =& new cGrid($refProgram->loadDatasource('refPracticeList'));
		$practiceGrid->indexCol = false;
		$this->view->assign_by_ref('practiceGrid', $practiceGrid);
		
		$this->view->assign('addPracticeLink', Celini::link('edit/0', 'refpractice', 'main') . 'program_id=' . $refProgram->get('id'));
		$this->view->assign("FORM_ACTION", Celini::link('edit', true, true, $refProgram->get('id')));
		return $this->view->fetch(Celini::getTemplatePath('/refprogram/' . $this->template_mod . '_edit.html'));
	}
	
	function processEdit($id = 0) {
		$refProgram =& Celini::newORDO('refProgram', $id);
		$refProgram->populate_array($_POST['refProgram']);
		$refProgram->persist();
		
		if (isset($_POST['process_refProgramMember']) && $_POST['process_refProgramMember'] == 'true') { 
			$refProgramMember =& Celini::newORDO('refProgramMember');
			$refProgramMember->populate_array($_POST['refProgramMember']);
			$refProgramMember->set('refprogram_id', $refProgram->get('id'));
			$refProgramMember->persist();
		}
		
		if (isset($_POST['refUser']) && 
			(!empty($_POST['refUser']['external_user_id']) && !empty($_POST['refUser']['refusertype']))
		) {
			$refUser =& Celini::newORDO('refUser');
			$refUser->populate_array($_POST['refUser']);
			$refUser->set('refprogram_id', $refProgram->get('id'));
			$refUser->persist();
			
			$person =& Celini::newORDO('Person', $refUser->get('external_user_id'));
			CeliniACL::addWho($person->get('username'));
			$groupName = strtolower(str_replace(' ', '_', $refUser->value('refusertype')));
			CeliniACL::addWhoToGroup($person->get('username'), $groupName);
		}
		
		$this->_refprogram_id = $refProgram->get('id'); 
	}
	
	function processRemoveUser() {
		$refUser =& Celini::newORDO('refUser', $this->GET->getTyped('refuser_id', 'int'));
		$refUser->set('deleted', 1);
		$refUser->persist();
		
		// check to see if this user has any permissions that require they keep their
		// current CeliniACL level, otherwise remove their ACL
		$existingRefUser =& Celini::newORDO(
			'refUser',
			array($refUser->get('external_user_id'), $refUser->value('refusertype')),
			'ByUserAndType'
		);
		
		if ($existingRefUser->get('deleted') > 0) {
			$groupName = strtolower(str_replace(' ', '_', $refUser->value('refusertype')));
			CeliniACL::dropWhoFromGroup($existingRefUser->value('username'), $groupName);
		}
		Celini::redirectURL($_SERVER['HTTP_REFERER']);
		exit;
	}
	
	function _returnFetch($name) {	
		
	}
}

