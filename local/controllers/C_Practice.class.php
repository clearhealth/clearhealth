<?php
require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";

/**
 * Controller for the Freestand Branch stuff
 */
class C_Practice extends Controller {

	var $number_id = 0;

	/**
	 * Edit/Add an Practice
	 *
	 */
	function edit_action_edit($practice_id = 0) {
		if (isset($this->practice_id)) {
			$practice_id = $this->practice_id;
		}

		$number_id = $this->number_id;

		$address_id = 0;
		if (isset($this->address_id)) {
			$address_id = $this->address_id;
		}

		$practice =& ORdataObject::factory('Practice',$practice_id);
		$number =& ORDataObject::factory('PracticeNumber',$number_id,$practice_id);
		$address =& ORDataObject::factory('PracticeAddress',$address_id,$practice_id);

		$this->assign_by_ref('practice',$practice);
		$this->assign_by_ref('parent',$practice);
		$this->assign_by_ref('user',$user);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign_by_ref('company',$company);
		$this->assign('FORM_ACTION',Cellini::managerLink('update',$practice_id));
		$this->assign('EDIT_NUMBER_ACTION',Cellini::managerLink('editNumber',$practice_id));
		$this->assign('DELETE_NUMBER_ACTION',Cellini::managerLink('deleteNumber',$practice_id));
		$this->assign('EDIT_ADDRESS_ACTION',Cellini::managerLink('editAddress',$practice_id));
		$this->assign('DELETE_ADDRESS_ACTION',Cellini::managerLink('deleteAddress',$practice_id));

		$this->assign('now',date('Y-m-d'));

		return $this->fetch(Cellini::getTemplatePath("/practice/" . $this->template_mod . "_edit.html"));
	}

	/**
	 * List Branches
	 */
	function list_action_view() {
		$person =& ORDataObject::factory('Practice');

		$ds =& $person->practiceList();
		$ds->template['name'] = "<a href='".Cellini::link('edit')."id={\$person_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/practice/" . $this->template_mod . "_list.html"));
	}

}
?>
