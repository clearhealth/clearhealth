<?php
require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";

/**
 * Controller for the Clearhealth users
 */
class C_User extends Controller {

	var $number_id = 0;
	var $address_id = 0;
	var $identifier_id = 0;
	var $provider_to_insurance_id = 0;

	/**
	 * Edit/Add a User
	 *
	 */
	function edit_action_edit($person_id = 0) {
		if (isset($this->person_id)) {
			$person_id = $this->person_id;
		}

		$person =& ORdataObject::factory('Person',$person_id);
		$number =& ORDataObject::factory('PersonNumber',$this->number_id,$person_id);
		$address =& ORDataObject::factory('PersonAddress',$this->address_id,$person_id);
		$identifier =& ORDataObject::factory('Identifier',$this->identifier_id,$person_id);
		$room =& ORdataObject::factory('Room');

		$user =& User::fromPersonId($person_id);
		if ($person->get('id') == 0) {
			$person->set_type(2);
		}

		if ($person->get('type') == 2) {
			$provider =& ORDataObject::factory('Provider',$person_id);
			$this->assign_by_ref('provider',$provider);

			$providerToInsurance =& ORDataObject::factory('ProviderToInsurance',$this->provider_to_insurance_id);
			$this->assign_by_ref('providerToInsurance',$providerToInsurance);

			$providerToInsuranceGrid =& new cGrid($providerToInsurance->providerToInsuranceList($person_id));
			$this->assign_by_ref('providerToInsuranceGrid',$providerToInsuranceGrid);

			$insuranceProgram =& ORDataObject::Factory('InsuranceProgram');
			$this->assign_by_ref('insuranceProgram',$insuranceProgram);
		}

		$nameHistoryGrid =& new cGrid($person->nameHistoryList());
		$identifierGrid =& new cGrid($person->identifierList());
		$identifierGrid->registerTemplate('identifier','<a href="'.Cellini::ManagerLink('editIdentifier',$person_id).'id={$identifier_id}&process=true">{$identifier}</a>');
		$identifierGrid->registerTemplate('actions','<a href="'.Cellini::ManagerLink('deleteIdentifier',$person_id).'id={$identifier_id}&process=true">delete</a>');
		$identifierGrid->setLabel('actions',false);
		

		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign_by_ref('user',$user);
		$this->assign_by_ref('identifier',$identifier);
		$this->assign_by_ref('nameHistoryGrid',$nameHistoryGrid);
		$this->assign_by_ref('identifierGrid',$identifierGrid);
		$this->assign("rooms_practice_array",$room->rooms_practice_factory());
		$this->assign('FORM_ACTION',Cellini::managerLink('update',$person_id));
		$this->assign('EDIT_NUMBER_ACTION',Cellini::managerLink('editNumber',$person_id));
		$this->assign('DELETE_NUMBER_ACTION',Cellini::managerLink('deleteNumber',$person_id));
		$this->assign('EDIT_ADDRESS_ACTION',Cellini::managerLink('editAddress',$person_id));
		$this->assign('DELETE_ADDRESS_ACTION',Cellini::managerLink('deleteAddress',$person_id));

		$this->assign('now',date('Y-m-d'));

		return $this->fetch(Cellini::getTemplatePath("/user/" . $this->template_mod . "_edit.html"));
	}

	/**
	 * List Users
	 */
	function list_action_view() {
		$person =& ORDataObject::factory('Person');

		$ds =& $person->peopleByType(array('Provider','Staff','Mid-Level'),true);
		$ds->template['last_name'] = "<a href='".Cellini::link('edit')."id={\$person_id}'>{\$last_name}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/user/" . $this->template_mod . "_list.html"));
	}


}
?>
