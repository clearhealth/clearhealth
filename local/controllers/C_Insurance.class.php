<?php
require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";

/**
 * Controller for the Insurance listing/editing
 */
class C_Insurance extends Controller {

	var $number_id = 0;
	var $address_id = 0;
	var $identifier_id = 0;
	var $insurance_program_id = 0;

	/**
	 * Edit/Add an Insurance Company
	 *
	 */
	function edit_action_edit($company_id = 0) {
		if (isset($this->company_id)) {
			$company_id = $this->company_id;
		}

		$company =& ORdataObject::factory('Company',$company_id);
		$number =& ORDataObject::factory('CompanyNumber',$this->number_id,$company_id);
		$address =& ORDataObject::factory('CompanyAddress',$this->address_id,$company_id);

		$insuranceProgram =& ORDataObject::factory('InsuranceProgram',$this->insurance_program_id);
		$insuranceProgramGrid =& new cGrid($insuranceProgram->detailedProgramList($company_id));

		$this->assign_by_ref('company',$company);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign_by_ref('insuranceProgram',$insuranceProgram);
		$this->assign_by_ref('insuranceProgramGrid',$insuranceProgramGrid);

		$this->assign('FORM_ACTION',Cellini::managerLink('update',$company_id));
		$this->assign('EDIT_NUMBER_ACTION',Cellini::managerLink('editNumber',$company_id));
		$this->assign('DELETE_NUMBER_ACTION',Cellini::managerLink('deleteNumber',$company_id));
		$this->assign('EDIT_ADDRESS_ACTION',Cellini::managerLink('editAddress',$company_id));
		$this->assign('DELETE_ADDRESS_ACTION',Cellini::managerLink('deleteAddress',$company_id));
		$this->assign('hide_type',true);

		$this->assign('now',date('Y-m-d'));

		return $this->fetch(Cellini::getTemplatePath("/insurance/" . $this->template_mod . "_edit.html"));
	}

	/**
	 * List Insurance Companies
	 */
	function list_action_view() {
		$company =& ORDataObject::factory('Company');

		$ds =& $company->companyListForType('Insurance');
		$ds->template['name'] = "<a href='".Cellini::link('edit')."id={\$company_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/insurance/" . $this->template_mod . "_list.html"));
	}


}
?>
