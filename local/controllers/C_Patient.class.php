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
	var $encounter_date_id = 0;
	var $encounter_person_id = 0;

	function C_Patient() {
		parent::Controller();
		$this->_load_controller_vars();	
	}
	
	/**
	 * Summary view showing patients forms, reports, encounters, summary
	 * demographics, prescriptions documents
	 *
	 */
	function dashboard_action($patient_id = "") {
		if (is_numeric($patient_id) && $patient_id > 0) {
			$this->set("patient_id",$patient_id);	
		} 
		
		if (is_numeric($this->get("patient_id")) && $this->get("patient_id") > 0){
			$this->set('external_id',$this->get('patient_id'));
			$p = ORDataObject::Factory("patient",$this->get("patient_id"));
			$number =& ORDataObject::factory('PersonNumber',$this->number_id,$patient_id);
			$address =& ORDataObject::factory('PersonAddress',$this->address_id,$patient_id);
			$insuredRelationship =& ORDataObject::factory('InsuredRelationship',$this->insured_relationship_id,$patient_id);
			$insuredRelationshipGrid =& new cGrid($p->insuredRelationshipList());

			$encounter =& ORDataObject::factory("Encounter");
			$encounterGrid =& new cGrid($encounter->encounterList($this->get('patient_id')));
			$encounterGrid->registerTemplate('date_of_treatment','<a href="'.Cellini::link('encounter').'id={$encounter_id}">{$date_of_treatment}</a>');
			$encounterGrid->pageSize = 5;

			$formData =& ORDataObject::factory("FormData");
			$formDataGrid =& new cGrid($formData->dataListByExternalId($this->get('patient_id')));
			$formDataGrid->registerTemplate('name','<a href="'.Cellini::link('data','Form').'id={$form_data_id}">{$name}</a>');
			$formDataGrid->pageSize = 10;

			$menu = Menu::getInstance();
			$tmp = $menu->getMenuData('patient',90);

			$formList = array();
			if (isset($tmp['forms'])) {
				foreach($tmp['forms'] as $form) {
					$formList[$form['form_id']] = $form['title'];
				}	
			}

			$report =& ORDataObject::factory("Report");
			$reportGrid = new cGrid($report->connectedReportList(89));
			$reportGrid->registerTemplate("title",'<a href="'.Cellini::link('report').'report_id={$report_id}&template_id={$report_template_id}">{$title}</a>');
			
			
			$this->assign_by_ref("person",$p);
			$this->assign_by_ref('number',$number);
			$this->assign_by_ref('address',$address);
			$this->assign_by_ref('insuredRelationship',$insuredRelationship);
			$this->assign_by_ref('insuredRelationshipGrid',$insuredRelationshipGrid);
			$this->assign_by_ref('encounterGrid',$encounterGrid);
			$this->assign_by_ref('formDataGrid',$formDataGrid);
			$this->assign_by_ref('reportGrid',$reportGrid);

			$this->assign('formList',$formList);

			$this->assign('ENCOUNTER_ACTION',Cellini::link('encounter'));
			$this->assign('FORM_FILLOUT_ACTION',Cellini::link('fillout','Form'));
			$this->assign('EDIT_ACTION',Cellini::link('edit',true,true,$this->get('patient_id')));
			
		}
		else {
			$this->messages->addMessage('There is no currently selected patient or an invalid patient number was supplied.');	
		}
		
		return $this->fetch(Cellini::getTemplatePath("/patient/" . $this->template_mod . "_dashboard.html"));
	}

	/**
	 * Edit/Add an Patient
	 *
	 */
	function edit_action_edit($patient_id = 0) {
		if (isset($this->patient_id)) {
			$patient_id = $this->patient_id;
		}

		$this->set('patient_id',$patient_id);

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
		$ds->template['name'] = "<a href='".Cellini::link('dashboard')."id={\$person_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/patient/" . $this->template_mod . "_list.html"));
	}

	/**
	 * Edit/Add an encounter
	 */
	function encounter_action_edit($encounter_id = 0) {
		if (isset($this->encounter_id)) {
			$encounter_id = $this->encounter_id;
		}
		if ($encounter_id > 0) {
			$this->set('encounter_id',$encounter_id);
		}
		if ($encounter_id == 0 && $this->get('encounter_id') > 0) {
			$encounter_id = $this->get('encounter_id');
		}	
		$this->set('external_id',$encounter_id);

		$encounter =& ORDataObject::factory('Encounter',$encounter_id,$this->get('patient_id'));
		$person =& ORDataObject::factory('Person');
		$building =& ORDataObject::factory('Building');

		$encounterDate =& ORDataObject::factory('EncounterDate',$this->encounter_date_id,$encounter_id);
		$encounterDateGrid = new cGrid($encounterDate->encounterDateList($encounter_id));
		$encounterDateGrid->registerTemplate('date','<a href="'.Cellini::Managerlink('editEncounterDate',$encounter_id).'id={$encounter_date_id}&process=true">{$date}</a>');

		$encounterPerson =& ORDataObject::factory('EncounterPerson',$this->encounter_person_id,$encounter_id);
		$encounterPersonGrid = new cGrid($encounterPerson->encounterPersonList($encounter_id));
		$encounterPersonGrid->registerTemplate('person','<a href="'.Cellini::Managerlink('editEncounterPerson',$encounter_id).'id={$encounter_person_id}&process=true">{$person}</a>');


		$formData =& ORDataObject::factory("FormData");
		$formDataGrid =& new cGrid($formData->dataListByExternalId($encounter_id));
		$formDataGrid->registerTemplate('name','<a href="'.Cellini::link('data','Form').'id={$form_data_id}">{$name}</a>');
		$formDataGrid->pageSize = 10;

		$menu = Menu::getInstance();
		$tmp = $menu->getMenuData('patient',91);

		$formList = array();
		if (isset($tmp['forms'])) {
			foreach($tmp['forms'] as $form) {
				$formList[$form['form_id']] = $form['title'];
			}	
		}


		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('building',$building);
		$this->assign_by_ref('encounterDate',$encounterDate);
		$this->assign_by_ref('encounterDateGrid',$encounterDateGrid);
		$this->assign_by_ref('encounterPerson',$encounterPerson);
		$this->assign_by_ref('encounterPersonGrid',$encounterPersonGrid);
		$this->assign_by_ref('formDataGrid',$formDataGrid);
		$this->assign_by_ref('formList',$formList);

		$this->assign('FORM_ACTION',Cellini::link('encounter',true,true,$encounter_id));
		$this->assign('FORM_FILLOUT_ACTION',Cellini::link('fillout','Form'));

		if ($encounter_id > 0) {
			require_once APP_ROOT ."/local/controllers/C_Coding.class.php";
			$coding = new C_Coding();
			$codingHtml = $coding->update_action($encounter_id);
			$this->assign('codingHtml',$codingHtml);
		}


		return $this->fetch(Cellini::getTemplatePath("/patient/" . $this->template_mod . "_encounter.html"));
	}

	function encounter_action_process($encounter_id=0) {
		if (isset($_POST['saveCode'])) {
			require_once APP_ROOT ."/local/controllers/C_Coding.class.php";
			$coding = new C_Coding();
			$coding->update_action_process();
			return;
		}

		$encounter =& ORDataObject::factory('Encounter',$encounter_id,$this->get('patient_id'));
		$encounter->populate_array($_POST['encounter']);
		
		if (isset($_POST['encounter']['close'])) {
			$encounter->set("status","closed");	
		}
		
		$encounter->persist();
		$this->encounter_id = $encounter->get('id');

		if (isset($_POST['encounterDate']) && !empty($_POST['encounterDate']['date'])) {
			$this->encounter_date_id = $_POST['encounterDate']['encounter_date_id'];
			$encounterDate =& ORDataObject::factory('EncounterDate',$this->encounter_date_id,$this->encounter_id);
			$encounterDate->populate_array($_POST['encounterDate']);
			$encounterDate->persist();
			$this->encounter_date_id = $encounterDate->get('id');
		}
		if (isset($_POST['encounterPerson']) && !empty($_POST['encounterPerson']['person_id'])) {
			$this->encounter_person_id = $_POST['encounterPerson']['encounter_person_id'];
			$encounterPerson =& ORDataObject::factory('EncounterPerson',$this->encounter_person_id,$this->encounter_id);
			$encounterPerson->populate_array($_POST['encounterPerson']);
			$encounterPerson->persist();
			$this->encounter_person_id = $encounterPerson->get('id');
		}
		
	}

}
?>
