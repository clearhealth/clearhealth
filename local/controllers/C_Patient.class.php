<?php
require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";

/**
 * Controller for the Freestand Branch stuff
 */
class C_Patient extends Controller {

	var $number_id = 0;
	var $address_id = 0;
	var $identifier_id = 0;
	var $insured_relationship_id = 0;
	var $person_person_id = 0;

	function dashboard_action() {
		if (isset($_SESSION['clearhealth']['active_user']) && $_SESSION['clearhealth']['active_user'] > 0) {

		}
	}

	/**
	 * Edit/Add an Patient
	 *
	 */
	function edit_action_edit($patient_id = 0) {
		if (isset($this->patient_id)) {
			$patient_id = $this->patient_id;
		}

		$person =& ORdataObject::factory('Patient',$patient_id);
		$number =& ORDataObject::factory('PersonNumber',$this->number_id,$patient_id);
		$address =& ORDataObject::factory('PersonAddress',$this->address_id,$patient_id);
		$identifier =& ORDataObject::factory('Identifier',$this->identifier_id,$patient_id);

		$nameHistoryGrid =& new cGrid($person->nameHistoryList());
		$identifierGrid =& new cGrid($person->identifierList());
		$identifierGrid->registerTemplate('identifier','<a href="'.Cellini::ManagerLink('editIdentifier',$patient_id).'id={$identifier_id}&process=true">{$identifier}</a>');
		$identifierGrid->registerTemplate('actions','<a href="'.Cellini::ManagerLink('deleteIdentifier',$patient_id).'id={$identifier_id}&process=true">delete</a>');
		$identifierGrid->setLabel('actions',false);

		$insuredRelationship =& ORDataObject::factory('InsuredRelationship',$this->insured_relationship_id,$patient_id);
		$insuredRelationshipGrid =& new cGrid($person->insuredRelationshipList());

		$insuranceProgram =& ORDataObject::Factory('InsuranceProgram');
		$this->assign_by_ref('insuranceProgram',$insuranceProgram);

		$personPerson =& ORDataObject::factory('PersonPerson',$this->person_person_id);
		$personPersonGrid = new cGrid($personPerson->relatedList($patient_id));
		//$personPersonGrid->registerTemplate('relation_type','<a href="'.Cellini::ManagerLink('editPersonPerson',$patient_id).'id={$person_person_id}&process=true">{$relation_type}</a>');
		

		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign_by_ref('identifier',$identifier);
		$this->assign_by_ref('nameHistoryGrid',$nameHistoryGrid);
		$this->assign_by_ref('identifierGrid',$identifierGrid);
		$this->assign_by_ref('insuredRelationship',$insuredRelationship);
		$this->assign_by_ref('insuredRelationshipGrid',$insuredRelationshipGrid);
		$this->assign_by_ref('personPerson',$personPerson);
		$this->assign_by_ref('personPersonGrid',$personPersonGrid);
		$this->assign('FORM_ACTION',Cellini::managerLink('update',$patient_id));
		$this->assign('EDIT_NUMBER_ACTION',Cellini::managerLink('editNumber',$patient_id));
		$this->assign('DELETE_NUMBER_ACTION',Cellini::managerLink('deleteNumber',$patient_id));
		$this->assign('EDIT_ADDRESS_ACTION',Cellini::managerLink('editAddress',$patient_id));
		$this->assign('DELETE_ADDRESS_ACTION',Cellini::managerLink('deleteAddress',$patient_id));
		$this->assign('hide_type',true);

		$this->assign('now',date('Y-m-d'));

		return $this->fetch(Cellini::getTemplatePath("/patient/" . $this->template_mod . "_edit.html"));
	}

	/**
	 * List Patients
	 */
	function list_action_view() {
		$person =& ORDataObject::factory('Patient');

		$ds =& $person->patientList();
		$ds->template['name'] = "<a href='".Cellini::link('edit')."id={\$person_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/patient/" . $this->template_mod . "_list.html"));
	}


}
?>
