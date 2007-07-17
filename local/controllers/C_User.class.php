<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('includes/colorpickerselect.class.php');
$loader->requireOnce('controllers/C_SecondaryPractice.class.php');
$loader->requireOnce('datasources/User_DS.class.php');

/**
 * Controller for the Clearhealth users
 */
class C_User extends Controller {

	var $number_id = 0;
	var $address_id = 0;
	var $identifier_id = 0;
	var $provider_to_insurance_id = 0;

	/**
	 * Display the add action
	 *
	 * @see actionEdit()
	 */
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	
	/**
	 * Edit/Add a User
	 *
	 */
	function actionEdit($person_id = 0) {
		if (isset($this->person_id)) {
			$person_id = $this->person_id;
		}
		
		if ($this->GET->exists('provider_to_insurance_id')) {
			$this->provider_to_insurance_id = $this->GET->getTyped('provider_to_insurance_id', 'int');
		}

		$person =& Celini::newORDO('Person',$person_id);
		$userProfile =& Celini::getCurrentUserProfile();
                $pid = $userProfile->getCurrentPracticeId();
		$prac =& Celini::newORDO('Practice',$person->get('primary_practice_id'));
		if ($person->get('person_id') > 0 && $person->get('primary_practice_id') != $pid) {
		$this->messages->addMessage('Your current practice selection must match the practice of this user/person in order to edit them. Their practice is: ' . $prac->get('name'));
                        return $this->fetch("main/general_message.html");
		}

		if($person_id == 0 && isset($_POST['person'])){
			$person->populateArray($_POST['person']);
			$person->persist();
		}
		
		$formAction = Celini::managerLink('update', $person_id);
		$this->assign('FORM_ACTION', $formAction);
		$this->assign('EDIT_NUMBER_ACTION',Celini::managerLink('editNumber',$person_id));
		$this->assign('DELETE_NUMBER_ACTION',Celini::managerLink('deleteNumber',$person_id));
		$this->assign('EDIT_ADDRESS_ACTION',Celini::managerLink('editAddress',$person_id));
		$this->assign('DELETE_ADDRESS_ACTION',Celini::managerLink('deleteAddress',$person_id));

		$number =& ORDataObject::factory('PersonNumber',$this->number_id,$person_id);
		$address =& ORDataObject::factory('PersonAddress',$this->address_id,$person_id);
		$identifier =& ORDataObject::factory('Identifier',$this->identifier_id,$person_id);
		$room =& ORdataObject::factory('Room');
		$up =& Celini::getCurrentUserProfile();
		$fid = array_values($up->getPracticeIdList());
		$roomlist = $room->rooms_practice_factory($fid);
		$this->assign('practiceList',$prac->genericList(false));
		if(count($roomlist) == 1) {
			$this->messages->addMessage('Please create a practice with a building and room before creating any users.');
			return;
		}
		$user =& User::fromPersonId($person_id);
		if ($person->get('id') == 0) {
			$person->set_type(2);
		}
		$roles = $user->getDisplayGroups('roles');
		$currentRoles = $user->get('selected_group_ids');
		$em =& Celini::enumManagerInstance();
		$templateRoles = $roles;
		
		if (isset($currentRoles[1]) && is_array($roles) && strlen($roles[$currentRoles[1]]) > 0) {
			if ($roles[$currentRoles[1]] == "Provider" || $roles[$currentRoles[1]] == "clinicadmin") {
		$permittedRoles = $em->enumArray('clinicadmin_permissions');
		$templateRoles = array();
		foreach ($roles as $key => $role) {
			if (array_search($role,$permittedRoles)) {
				$templateRoles[$key] = $role;
			}
			
		}
		}
		}
		$this->assign("roles",$templateRoles);
		$picker =& new colorPickerSelect('pastels','user[color]','','#'.$user->get('color'));
		$picker->id = 'colorPicker';
		$this->view->assign_by_ref('colorpicker',$picker);
		if ($person->get('type') == 2) {
			$provider =& Celini::newORDO('Provider',$person_id);
			$this->view->assign_by_ref('provider',$provider);

			$providerToInsurance =& Celini::newORDO('ProviderToInsurance',$this->provider_to_insurance_id);
			$this->view->assign_by_ref('providerToInsurance',$providerToInsurance);

			$providerToInsuranceGrid =& new cGrid($providerToInsurance->providerToInsuranceList($person_id));
			$providerToInsuranceGrid->setLabel('edit', false);
			$providerToInsuranceGrid->registerTemplate('edit', 
				'<a href="' . Celini::link(true, true) . 'person_id=' . $person_id . '&provider_to_insurance_id={$provider_to_insurance_id}">Edit</a>'
			);
			
			$this->view->assign_by_ref('providerToInsuranceGrid',$providerToInsuranceGrid);

			$insuranceProgram =& Celini::newORDO('InsuranceProgram');
			$this->view->assign_by_ref('insuranceProgram',$insuranceProgram);
			
			$building =& Celini::newORDO('Building');
			$this->view->assign_by_ref('building', $building);
		}
		
		// Generate view for SecondaryPractice
		$cSecondaryPractice =& new C_SecondaryPractice();
		$cSecondaryPractice->view->assign('FORM_ACTION', $formAction);
		$cSecondaryPractice->person =& $person;
		$this->view->assign('secondaryPracticeView', $cSecondaryPractice->actionDefault());
		

		$nameHistoryGrid =& new cGrid($person->nameHistoryList());
		$identifierGrid =& new cGrid($person->identifierList());
		$identifierGrid->registerTemplate('identifier','<a href="'.Celini::ManagerLink('editIdentifier',$person_id).'id={$identifier_id}&process=true">{$identifier}</a>');
		$identifierGrid->registerTemplate('actions','<a href="'.Celini::ManagerLink('deleteIdentifier',$person_id).'id={$identifier_id}&process=true">delete</a>');
		$identifierGrid->setLabel('actions',false);
		

		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign_by_ref('user',$user);
		$this->assign_by_ref('identifier',$identifier);
		$this->assign_by_ref('nameHistoryGrid',$nameHistoryGrid);
		$this->assign_by_ref('identifierGrid',$identifierGrid);
		$this->assign("rooms_practice_array",$roomlist);
		$this->assign('now',date('Y-m-d'));

		return $this->view->render('edit.html');
	}

	/**
	 * List Users
	 */
	function list_action_view() {
		$ds =& new User_DS();
		$grid =& new cGrid($ds);
		$grid->name = "userGrid";
		$this->view->assign_by_ref('grid',$grid);
		return $this->view->render("list.html");
	}


	/**
	 * Update the password of the currently logged in user
	 */
	function password_action_edit() {
		$user =& $this->_me->get_user();

		$this->assign_by_ref('user',$user);
		
		return $this->view->render("password.html");
	}

	function password_action_process() {
		$user =& $this->_me->get_user();

		if ($_POST['password']['current_password'] !== $user->get('password')) {
			$this->messages->addMessage('Current Password Incorrect');
			return "";
		}
		$user->set('password',$_POST['password']['password']);
		$user->persist();
	}
}
?>
