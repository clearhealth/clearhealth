<?php
require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";

/**
 * Controller for the Freestand Branch stuff
 */
class C_Patient extends Controller {

	var $number_id = 0;

	/**
	 * Edit/Add an Patient
	 *
	 */
	function edit_action_edit($patient_id = 0) {
		if (isset($this->patient_id)) {
			$patient_id = $this->patient_id;
		}

		$number_id = $this->number_id;

		$address_id = 0;
		if (isset($this->address_id)) {
			$address_id = $this->address_id;
		}

		$person =& ORdataObject::factory('Patient',$patient_id);
		$number =& ORDataObject::factory('PersonNumber',$number_id,$patient_id);
		$address =& ORDataObject::factory('PersonAddress',$address_id,$patient_id);

		ORDataObject::factory_include('User');
		$u = new User();
		$user =& $u->fromPersonId($patient_id);

		if (!$person->get('type') > 0) {
			$lookup = array_flip($person->getTypeList());
			$person->set('types',array($lookup['Loan Officer']));
		}

		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('user',$user);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign('FORM_ACTION',Cellini::managerLink('update',$patient_id));
		$this->assign('EDIT_NUMBER_ACTION',Cellini::managerLink('editNumber',$patient_id));
		$this->assign('DELETE_NUMBER_ACTION',Cellini::managerLink('deleteNumber',$patient_id));
		$this->assign('EDIT_ADDRESS_ACTION',Cellini::managerLink('editAddress',$patient_id));
		$this->assign('DELETE_ADDRESS_ACTION',Cellini::managerLink('deleteAddress',$patient_id));

		$this->assign('now',date('Y-m-d'));

		return $this->fetch(Cellini::getTemplatePath("/patient/" . $this->template_mod . "_edit.html"));
	}

	/**
	 * List Branches
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
