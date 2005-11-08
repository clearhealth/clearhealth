<?php

/**
 * Patient Dashboard
 */
class C_PatientDashboard extends Controller {

	var $number_id = 0;
	var $address_id = 0;
	var $insured_relationship_id = 0;
	var $note_id = 0;

	/**
	 * Summary view showing patients forms, reports, encounters, summary
	 * demographics, prescriptions documents
	 *
	 */
	function actionView($patient_id = "") {
		if (is_numeric($patient_id) && $patient_id > 0) {
			if ($this->get('patient_id') != $patient_id) {
				$this->set("encounter_id",false);	
			}
			$this->set("patient_id",$patient_id);	
		} 
		
		if (is_numeric($this->get("patient_id")) && $this->get("patient_id") > 0){
			$this->set('external_id',$this->get('patient_id'));
			$p = ORDataObject::Factory("Patient",$this->get("patient_id"));
			$number =& ORDataObject::factory('PersonNumber',$this->number_id,$patient_id);
			$address =& ORDataObject::factory('PersonAddress',$this->address_id,$patient_id);
			$insuredRelationship =& ORDataObject::factory('InsuredRelationship',$this->insured_relationship_id,$patient_id);
			$insuredRelationshipGrid =& new cGrid($p->loadDatasource('InsuredRelationshipList'));
			$insuredRelationshipGrid->name = "insuredRelationshipGrid";
			$insuredRelationshipGrid->indexCol = false;
			$insuredRelationshipGrid->setExternalId($p->get('person_id'));

			$encounterGrid =& new cGrid($p->loadDatasource('EncounterList'));
			$encounterGrid->name = "encounterGrid";
			$encounterGrid->registerTemplate('date_of_treatment','<a href="'.Celini::link('edit', 'encounter').'id={$encounter_id}">{$date_of_treatment}</a>');
			$encounterGrid->pageSize = 5;
			$encounterGrid->setExternalId($p->get('id'));

			$formDataGrid =& new cGrid($p->loadDatasource('FormDataList'));
			$formDataGrid->name = "formDataGrid";
			$formDataGrid->registerTemplate('name','<a href="'.Celini::link('data','Form').'id={$form_data_id}">{$name}</a>');
			$formDataGrid->pageSize = 10;
			$formDataGrid->setExternalId($p->get('id'));
			
			$menu = Menu::getInstance();
			$tmp = $menu->getMenuData('patient',90);

			$formList = array();
			if (isset($tmp['forms'])) {
				foreach($tmp['forms'] as $form) {
					$formList[$form['form_id']] = $form['title'];
				}	
			}

			$report =& ORDataObject::factory("Report");
			$reportGrid = new cGrid($report->loadDatasource('ConnectedList', 89));
			$reportGrid->name = "reportGrid";
			$reportGrid->registerTemplate("title",'<a href="'.Celini::link('report').'report_id={$report_id}&template_id={$report_template_id}">{$title}</a>');
			$reportGrid->setExternalId(89);

			$note =& ORDataObject::factory('PatientNote');
			$noteGrid =& new cGrid($p->loadDatasource('NoteList'));
			$noteGrid->pageSize = 10;
			$noteGrid->indexCol = false;
			$noteGrid->setExternalId($p->get('id'));
			
			$clearhealth_claim = ORDataObject::factory("ClearhealthClaim");
			$accountStatus = $clearhealth_claim->accountStatus($this->get("patient_id"));

			$appointmentDS =& $p->loadDatasource('Appointment');
			$appointmentGrid =& new cGrid($appointmentDS);
			$appointmentGrid->pageSize = 10;
			$appointmentGrid->setExternalId($p->get('id'));
			
			$this->assign_by_ref("person",$p);
			$this->assign_by_ref('number',$number);
			$this->assign_by_ref('address',$address);
			$this->assign_by_ref('insuredRelationship',$insuredRelationship);
			$this->assign_by_ref('insuredRelationshipGrid',$insuredRelationshipGrid);
			$this->assign_by_ref('encounterGrid',$encounterGrid);
			$this->assign_by_ref('formDataGrid',$formDataGrid);
			$this->assign_by_ref('reportGrid',$reportGrid);
			$this->assign_by_ref('accountStatus',$accountStatus);
			$this->assign_by_ref('noteGrid',$noteGrid);
			$this->assign_by_ref('depnoteGrid',$depnoteGrid);
			$this->assign_by_ref('note',$note);
			$this->assign_by_ref('appointmentGrid',$appointmentGrid);

			$this->assign('formList',$formList);

			$this->assign('ENCOUNTER_ACTION',Celini::link('add','Encounter') . 'patient_id=' . $p->get('id'));
			$this->assign('ACCOUNT_ACTION',Celini::link('history','account',true,$this->get("patient_id")));
			$this->assign('FORM_FILLOUT_ACTION',Celini::link('fillout','Form'));
			$this->assign('EDIT_ACTION',Celini::link('edit','Patient',true,$this->get('patient_id')));
			$this->assign('NO_PATIENT', false);			
			$this->assign('NOTE_ACTION',Celini::managerLink('note',$this->get('patient_id')));
			$this->assign('DELETE_NUMBER_ACTION',Celini::managerLink('deleteNumber',$patient_id));
			$this->assign('DELETE_ADDRESS_ACTION',Celini::managerLink('deleteAddress',$patient_id));
		}
		else {
			$this->assign('NO_PATIENT', true);
			$this->messages->addMessage('There is no currently selected patient or an invalid patient number was supplied.');	
		}
		
		return $this->view->render("view.html");
	}
}
?>
