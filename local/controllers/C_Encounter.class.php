<?php
$loader->requireOnce('controllers/C_Coding.class.php');
$loader->requireOnce('controllers/C_FreeBGateway.class.php');
$loader->requireOnce('includes/freebGateway/CHToFBArrayAdapter.class.php');
$loader->requireOnce('includes/LockManager.class.php');
$loader->requireOnce('datasources/MiscCharge_Encounter_DS.class.php');
$loader->requireOnce('datasources/Encounter_PayerGroup_DS.class.php');

/**
 * A patient Encounter
 */
class C_Encounter extends Controller {
	var $coding;
	var $coding_parent_id = 0;
	var $encounter_date_id = 0;
	var $encounter_value_id = 0;
	var $encounter_person_id = 0;
	var $payment_id = 0;
	var $coding_data_id = 0;
	var $edit_icd_code = 0;

	function C_Encounter() {
		$this->controller();
		$this->coding = new C_Coding();
	}

	function actionAdd() {
		if ($this->get('patient_id', 'c_patient') <= 0) {
			$this->messages->addMessage(
				'No Patient Selected', 
				'Please select a patient before attempting to add an encounter.');
			Celini::redirect('PatientFinder', 'List');
		}
		
		return $this->actionEdit();
	}

	/**
	 * Edit/Add an encounter
	 */
	function actionEdit($encounter_id = 0) {
		if (isset($this->encounter_id)) {
			$encounter_id = $this->encounter_id;
		}
		
		$encounter_id = $this->_enforcer->int($encounter_id);
		
		$appointment_id = $this->GET->getTyped('appointment_id', 'int');
		$patient_id = $this->GET->getTyped('patient_id', 'int');
		
		$valid_appointment_id = false;

		$ajax =& Celini::AJAXInstance();
		$ajax->stubs[] = 'Encounter';

		// check if an encounter_id already exists for this appointment
		if ($appointment_id > 0) {
			$valid_appointment_id = true;
			ORDataObject::factory_include('Encounter');
			$id = Encounter::encounterIdFromAppointmentId($appointment_id);
			if ($id > 0) {
				$encounter_id         = $id;
				$valid_appointment_id = false;
			} 
		}

		if ($encounter_id > 0) {
			$this->assign('lockTimestamp',time());
			$this->set('encounter_id',$encounter_id);
			$this->set('external_id', $this->get('encounter_id'),'c_patient');
		}
		if ($patient_id > 0) {
			$this->set('patient_id',$patient_id,'c_patient');
		}

		$this->set('encounter_id',$encounter_id);
		$encounter =& Celini::newORDO('Encounter',array($encounter_id,$this->get('patient_id', 'c_patient')));

		$appointments = $encounter->appointmentList();
		$appointmentArray = array("" => " ");
		foreach($appointments as $appointment) {
			$appointmentArray[$appointment['occurence_id']] = date("m/d/Y H:i",strtotime($appointment['appointment_start'])) . " " . $appointment['building_name'] . "->" . $appointment['room_name'] . " " . $appointment['provider_name'];
		}
		//
		//if an appointment id is supplied the request is coming from the 
		//calendar and so prepopulate the defaults
		if ($appointment_id > 0 && $valid_appointment_id) {
			$encounter->set("occurence_id",$appointment_id);
			$encounter->set("patient_id",$this->get("patient_id", 'c_patient'));
			if (isset($appointments[$appointment_id])) {
				$encounter->set("building_id",$appointments[$appointment_id]['building_id']);
			}
			if (isset($appointments[$appointment_id])) {
				$encounter->set("treating_person_id",$appointments[$appointment_id]['provider_id']);

				$em =& Celini::enumManagerInstance();
				$reason = $em->lookupKey('encounter_reason',$appointments[$appointment_id]['reason']);
				$encounter->set("encounter_reason",$reason);
			}
		}

		
		//if ($encounter_id == 0 && $this->get('encounter_id') > 0) {
		//	$encounter_id = $this->get('encounter_id');
		//}	
		if($encounter_id == 0) {
			$encounter->persist();
			// Default to default payer group
			// Setting 'payer_group' sets up the current payer too.
			$encounter->set('payer_group',1);

			if ($appointment_id > 0) {
				$encounter->set('occurence_id',$appointment_id);
			}
			$encounter->persist();
			$encounter_id = $encounter->get('id');
			$this->set('encounter_id',$encounter_id);
		}
		$person =& Celini::newORDO('Person');
		$building =& Celini::newORDO('Building',$encounter->get('building_id'));
		$practice =& Celini::newORDO('Practice',$building->get('practice_id'));
		$encounterDate =& Celini::newORDO('EncounterDate',array($this->encounter_date_id,$encounter_id));
		$encounterDateGrid = new cGrid($encounterDate->encounterDateList($encounter_id));
		$encounterDateGrid->name = "encounterDateGrid";
		$encounterDateGrid->registerTemplate('date','<a href="'.Celini::Managerlink('editEncounterDate',$encounter_id).'id={$encounter_date_id}&process=true">{$date}</a>');
		$session =& Celini::sessionInstance();
		$session->set('Encounter:practice_id',$practice->get('id'));


		$this->assign('NEW_ENCOUNTER_DATE',Celini::managerLink('editEncounterDate',$encounter_id)."id=0&process=true");

		$encounterValue =& Celini::newORDO('EncounterValue',array($this->encounter_value_id,$encounter_id));
		$encounterValueGrid = new cGrid($encounterValue->encounterValueList($encounter_id));
		$encounterValueGrid->name = "encounterValueGrid";
		$encounterValueGrid->registerTemplate('value','<a href="'.Celini::Managerlink('editEncounterValue',$encounter_id).'id={$encounter_value_id}&process=true">{$value}</a>');
		$this->assign('NEW_ENCOUNTER_VALUE',Celini::managerLink('editEncounterValue',$encounter_id)."id=0&process=true");

		$encounterPerson =& Celini::newORDO('EncounterPerson',array($this->encounter_person_id,$encounter_id));
		$encounterPersonGrid = new cGrid($encounterPerson->encounterPersonList($encounter_id));
		$encounterPersonGrid->name = "encounterPersonGrid";
		$encounterPersonGrid->registerTemplate('person','<a href="'.Celini::Managerlink('editEncounterPerson',$encounter_id).'id={$encounter_person_id}&process=true">{$person}</a>');
		$this->assign('NEW_ENCOUNTER_PERSON',Celini::managerLink('editEncounterPerson',$encounter_id)."id=0&process=true");
		
		$insuredRelationship =& Celini::newORDO('InsuredRelationship',
			array(
				(int)$encounter->get('current_payer'),
				(int)$encounter->get('patient_id')
			),
			'ByInsuranceProgramAndPerson'
		);
		$this->assign('copay', $insuredRelationship->get('copay'));
		
		$payment =& Celini::newORDO('Payment',$this->payment_id);
		if ($payment->_populated == false) {
			$payment->set('title','Co-Pay');
		}
		$payment->set("encounter_id",$encounter_id);
		$payments = $payment->paymentsFromEncounterId($encounter_id);
		$paymentGrid = new cGrid($payments);
		$paymentGrid->name = "paymentGrid";
		$paymentGrid->registerTemplate('amount','<a href="'.Celini::managerLink('editPayment',$encounter_id,'edit','Encounter').'id={$payment_id}&process=true">{$amount}</a>');
		$paymentGrid->registerFilter('payment_date', array('DateObject', 'ISOToUSA'));
		$this->assign('NEW_ENCOUNTER_PAYMENT',Celini::managerLink('editPayment',$encounter_id)."id=0&process=true");
		
		$payerGroupds = new Encounter_PayerGroup_DS($encounter->get('patient_id'),$encounter->get('payer_group'));
		$payergroupGrid =& new cGrid($payerGroupds);
		$payergroupGrid->indexCol = false;
		$payergroupGrid->orderLinks = false;
		$payergroupGrid->name = "encounterPayerGroupGrid";
		$this->view->assign_by_ref('payergroupGrid',$payergroupGrid);

		$miscChargeGrid = new cGrid(new MiscCharge_Encounter_DS($encounter_id));
		$this->assign_by_ref('miscChargeGrid',$miscChargeGrid);
		
		// If this is a saved encounter, generate the following:
		if ($this->get('encounter_id') > 0) {
			// Load data that has been stored
			$formData =& Celini::newORDO("FormData");
			$formDataGrid =& new cGrid($formData->dataListByExternalId($encounter_id));
			$formDataGrid->name  = "formDataGrid";
			$formDataGrid->registerTemplate('name','<a href="'.Celini::link('data','Form').'id={$form_data_id}">{$name}</a>');
		// commenting this line out fixed 3602 
		//	$formDataGrid->pageSize = 10;
		// 	
			// Generate a menu of forms that are connected to Encounters
			$menu = Menu::getInstance();
			$connectedForms = $menu->getMenuData('patient',
				$menu->getMenuIdFromTitle('patient','Encounter Forms'));
			
			$formList = array();
			if (isset($connectedForms['forms'])) {
				foreach($connectedForms['forms'] as $form) {
					$formList[$form['form_id']] = $form['title'];
				}
			}
		}
		
		$reports = array();
		if ($encounter->get('patient_id') > 0) {
			$pcc =& Celini::newOrdo('PatientChronicCode');
			$tmp = $pcc->PatientReportArray($encounter->get('patient_id'),false);
	
			foreach($tmp as $code => $r) {
				$t = "";
				foreach($r as $k => $reportData) {
					$t .= "report_id[$k]=$reportData[report_id]&report_template_id[$k]=$reportData[report_template_id]&";
				}
				$reports[] = array('name'=>$code,'num'=>count($r),'url'=>Celini::link('batch','Report').$t);
			}
		}
		$this->assign('encounterBatchReports',$reports);


		$this->assign_by_ref('insuredRelationship',$insuredRelationship);
		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('building',$building);
		$this->assign_by_ref('encounterDate',$encounterDate);
		$this->assign_by_ref('encounterDateGrid',$encounterDateGrid);
		$this->assign_by_ref('encounterPerson',$encounterPerson);
		$this->assign_by_ref('encounterPersonGrid',$encounterPersonGrid);
		$this->assign_by_ref('encounterValue',$encounterValue);
		$this->assign_by_ref('encounterValueGrid',$encounterValueGrid);
		$this->assign_by_ref('payment',$payment);
		$this->assign_by_ref('payments',$payments);
		$this->assign_by_ref('paymentGrid',$paymentGrid);
		$this->assign_by_ref('appointmentList',$appointments);
		$this->assign_by_ref('appointmentArray',$appointmentArray);
		
		$this->assign('FORM_ACTION',Celini::link('edit',true,true,$encounter_id));
		$this->assign('FORM_FILLOUT_ACTION',Celini::link('fillout','Form'));
		$this->assign('RETURN_TO',Celini::link('edit',true,true,$encounter_id));
		$this->assign('DELETE_ACTION',Celini::link('delete',true,true,$encounter_id));

		$pconfig=&$practice->get_config();
		if($pconfig->get('FacilityType',FALSE)){
			$this->coding->assign('dentalpractice',true);
			$this->coding->assign('teetharray',array(
				'N/A'=>'N/A',
				'All'=>'All',
				1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,
				16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,
				28=>28,29=>29,30=>30,31=>31,32=>32,
				'All (Primary)'=>'All (Primary)',
				'A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J',
				'K'=>'K','L'=>'L','M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R','S'=>'S','T'=>'T'
			));
			$this->coding->assign('toothsidearray',array('N/A'=>'N/A','Front'=>'Front','Back'=>'Back','Top'=>'Top','Left'=>'Left','Right'=>'Right'));
		}

		if ($encounter_id > 0) {
			$this->coding->assign('FORM_ACTION',Celini::link('edit',true,true,$encounter_id));
			$this->coding->assign("encounter", $encounter);
			$codingHtml = $this->coding->update_action_edit($encounter_id,$this->coding_data_id,$this->edit_icd_code);
			$this->assign('codingHtml',$codingHtml);
			$this->assign_by_ref('formDataGrid',$formDataGrid);
			$this->assign_by_ref('formList',$formList);
		}

		if ($encounter->get('status') === "closed") {
			ORDataObject::factory_include('ClearhealthClaim');
			$claim =& ClearhealthClaim::fromEncounterId($encounter_id);
			//printf('<pre>%s</pre>', var_export($claim->toArray(), true));
			$this->assign('FREEB_ACTION',Celini::link('list_revisions','Claim',true,$claim->get('identifier')));
			$this->assign('PAYMENT_ACTION',Celini::link('payment','Eob',true,$claim->get('id')));

			$this->assign('encounter_has_claim',false);
			if ($claim->_populated) {
				$this->assign('encounter_has_claim',true);
			}

			$this->assign('REOPEN_ACTION',Celini::link('reopen', true, true, $encounter->get('id')) . 'process=true');
		}
		else {
			ORdataObject::factory_include('ClearhealthClaim');
			$claim =& ClearhealthClaim::fromEncounterId($encounter_id);
			$this->assign('encounter_has_claim',false);
			if ($claim->get('identifier') > 0) {
				$this->assign('claimSubmitValue', 'rebill');
				$this->assign('encounter_has_claim',true);
			}
			else {
				$this->assign('claimSubmitValue', 'close');
			}
		}

		// before we view, make sure the current patient_id is setup for display with the current
		// encounter's patient info.
		if ($encounter->get('patient_id') > 0) {
			$this->set('patient_id', $encounter->get('patient_id'), 'c_patient');
		}
		
		$head =& Celini::HTMLheadInstance();
		$head->addExternalCss('suggest');
		return $this->view->render("edit.html");
	}

	
	function processEdit($encounter_id=0) {
		if (isset($_POST['saveCode'])) {
			$this->coding->update_action_process();
			return;
		} elseif(isset($_POST['updateCode'])) {
			$key = array_keys($_POST['updateCode']);
			$key = $key[0];
			$this->coding->coding_data_id = $key;
			$this->coding->update_action_process();
			return;
		}
		// lock check
		$lockTimestamp = $this->POST->get('lockTimestamp');

		if (!empty($lockTimestamp)) {

			$changes = array();
			$ordoType = 'Encounter';
			$changes['encounter'] = LockManager::hasOrdoChanged($ordoType,$encounter_id,$lockTimestamp);

			$tmp = LockManager::hasOrdoChanged($ordoType,$encounter_id,$lockTimestamp);
			$changes['encounter'] = array_merge($changes['encounter'],$tmp);
			if(isset($changes['encounter']['date_of_treatment'])) {
				$changes['encounter']['date_of_treatment']['old_value'] = date('m/d/Y',strtotime($changes['encounter']['date_of_treatment']['old_value']));
			}
			if(isset($changes['encounter']['date_of_treatment']) && strtotime($_POST['encounter']['date_of_treatment']) == strtotime($changes['encounter']['date_of_treatment']['new_value'])) {
				unset($changes['encounter']['date_of_treatment']);
			}
			if(isset($changes['encounter']['occurence_id']) && $changes['encounter']['occurence_id']['new_value']== $_POST['encounter']['occurence_id']) {
				unset($changes['encounter']['occurence_id']);
			}
			if(isset($changes['encounter']['current_payer'])) {
				unset($changes['encounter']['current_payer']);
			}
			// rest of this changes processing is generic and should be movable
			$subOrdos = array(
			'encounterDate' => array('EncounterDate','encounter_date_id'),
			'encounterValue' => array('EncounterValue','encounter_value_id'),
			'encounterPerson' => array('EncounterPerson','encounter_person_id'),
			'payment' => array('Payment','payment_id'),
			'misc_charge' => array('MiscCharge','misc_charge_id'),
			);

			foreach($subOrdos as $key => $info) {
				$ordoName = $info[0];
				$fieldName = $info[1];
				if (isset($_POST[$key][$fieldName]) && !empty($_POST[$key][$fieldName])) {
					$changes[$key] = LockManager::hasOrdoChanged($ordoName,$_POST[$key][$fieldName],$lockTimestamp);
				}
			}

			$overlappingChanges = false;
			foreach($changes as $name => $change) {
				if (count($change) > 0) {
					$overlappingChanges = true;
				}
			}
			if ($overlappingChanges) {
				$changes['_POST'] = $_POST;
				LockManager::prepareChangesAlert($changes,$this,$lockTimestamp);
				return;
			}
		}

		$encounter =& Celini::newORDO('Encounter',array($encounter_id,$this->get('patient_id', 'c_patient')));
		$encounter->populate_array($_POST['encounter']);

		$newencounter = false;
		if($encounter_id == 0) {
			$newencounter = true;
		}
		
		$encounter->persist();
		if (isset($_POST['select_payer']) || isset($_POST['select_payer_group'])) {
			return;
		}
		
		if($this->POST->exists('PatientPaymentPlan')) {
			$plan =& Celini::newORDO('PatientPaymentPlan');
			$plan->populate_array($this->POST->getRaw('PatientPaymentPlan'));
			$plan->persist();
			$plan->setParent($encounter);
		}
		$this->encounter_id = $encounter->get('id');
		$_GET[0] = $this->encounter_id;

		$manager =& EnumManager::getInstance();
		
		if($newencounter) {
			// Find the encounter template, if set
			$list =& $manager->enumList('encounter_reason');
			$reason = false;
			for($list->rewind();$list->valid();$list->next()) {
				$row = $list->current();
				if ($row->key == $encounter->get('encounter_reason')) {
					$reason = $row;
				}
			}
			if ($reason && $reason->extra1 !== '') {
				$template = Celini::newOrdo('CodingTemplate',$reason->extra1);
				$pcode =& Celini::newORDO('CodingData',$template->get('coding_parent_id'));
				$code_data =& ORDataObject::factory('CodingData');

				$child_codes = $code_data->getCodeList($template->get('id'));
				foreach($child_codes as $code) {
					$code_list = $pcode->getChildCodes($code['coding_data_id']);
					$code['coding_data_id'] = 0;
					$code['foreign_id'] = $this->encounter_id;
					$xcode =& Celini::newORDO('CodingData');
					$xcode->populate_array($code);
					$xcode->persist();
					foreach($code_list as $icdcode) {
						$icdcode['coding_data_id'] = 0;
						$icdcode['foreign_id'] = $this->encounter_id;
						$icdcode['parent_id'] = $xcode->get('id');
						$ycode =& Celini::newORDO('CodingData');
						$ycode->populateArray($icdcode);
						$ycode->persist();
					}
				}
			}
			
		}

		if (isset($_POST['encounterDate']) && !empty($_POST['encounterDate']['date'])) {
			$this->encounter_date_id = $_POST['encounterDate']['encounter_date_id'];
			$encounterDate =& Celini::newORDO('EncounterDate',array($this->encounter_date_id,$this->encounter_id));
			$encounterDate->populate_array($_POST['encounterDate']);
			$encounterDate->persist();
			$this->encounter_date_id = $encounterDate->get('id');
		}
		if (isset($_POST['encounterValue']) && !empty($_POST['encounterValue']['value'])) {
			$this->encounter_value_id = $_POST['encounterValue']['encounter_value_id'];
			$encounterValue =& Celini::newORDO('EncounterValue',array($this->encounter_value_id,$this->encounter_id));
			$encounterValue->populate_array($_POST['encounterValue']);
			$encounterValue->persist();
			$this->encounter_value_id = $encounterValue->get('id');
		}
		if (isset($_POST['encounterPerson']) && !empty($_POST['encounterPerson']['person_id'])) {
			$this->encounter_person_id = $_POST['encounterPerson']['encounter_person_id'];
			$encounterPerson =& Celini::newORDO('EncounterPerson',array($this->encounter_person_id,$this->encounter_id));
			$encounterPerson->populate_array($_POST['encounterPerson']);
			$encounterPerson->persist();
			$this->encounter_person_id = $encounterPerson->get('id');
		}
		if (isset($_POST['payment']) && !empty($_POST['payment']['amount'])) {
			$this->payment_id = $_POST['payment']['payment_id'];
			if(isset($_POST['newPayment'])) {
				$payment =& Celini::newORDO('Payment');
			} else {
				$payment =& Celini::newORDO('Payment',$this->payment_id);
			}
			$payment->set('encounter_id',$this->encounter_id);
			$payment->populate_array($_POST['payment']);
			if(isset($_POST['newPayment'])) {
				$payment->set('id',0);
			}
			$payment->persist();
			$this->payment_id = $payment->get('id');
		}
		if (isset($_POST['misc_charge']) && !empty($_POST['misc_charge']['title'])) {
			$miscCharge =& Celini::newOrdo('MiscCharge');
			$miscCharge->populateArray($_POST['misc_charge']);
			$miscCharge->set('encounter_id',$this->encounter_id);
			$miscCharge->set('charge_date',date('Y-m-d H:i:s'));
			$miscCharge->persist();
		}
		
		if (isset($_POST['encounter']['rebillfromscratch']) && $_POST['encounter']['rebillfromscratch'] == "true") {
			if($encounter->get('current_payer') < 1) {
				$this->messages->addMessage('Encounter has no payers available.');
				return;
			}
			$encounter->set('status', 'open');
			$encounter->persist();
			$sql = "
			DELETE FROM clearhealth_claim 
			WHERE
				encounter_id = ".$encounter->get('id');
			$db =& Celini::dbInstance();
			$db->execute($sql);
			$sql = "
			UPDATE fbclaim
			SET
				status='deleted'
			WHERE
				claim_identifier LIKE '%-%-".$encounter->get('id')."'";
			$db->execute($sql);
			$this->messages->addMessage('Previous Claim Deleted');
		}

		if (isset($_POST['encounter']['close'])) {
			$patient =& Celini::newORDO('Patient',$encounter->get('patient_id'));
			ORDataObject::Factory_include('InsuredRelationship');
			$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

			if ($relationships == null) { 
				$this->messages->addMessage("This Patient has no Insurance Information, please add insurance information and try again <br>");
				return;
			}else{	
				$encounter->set('status', 'closed');
				$encounter->persist();
				$this->_generateClaim($encounter);
			}
		}
		else if (isset($_POST['encounter']['override'])) {
			$billtype = $_POST['encounter']['overridebilltype'];
			$encounter->set('current_payer',$_POST['encounter']['overridepayer']);
			$encounter->set('status', 'closed');
			$encounter->persist();
			if($billtype == 'close') {
				$this->_generateClaim($encounter);
			} else {
				$this->_handleRebill($encounter);
			}
		}
		// If this is a rebill, pass it off to the rebill method
		else if (isset($_POST['encounter']['rebill'])) {
			$encounter->set('status', 'closed');
			$encounter->persist();
			$this->_handleRebill($encounter);
		}
		// If we're rebilling the next payer in the group, set the next biller
		else if(isset($_POST['encounter']['rebillnext'])) {
			$encounter->set('current_group_payer',$this->POST->get('rebillnextpayer'));
			$encounter->persist();
		}
	}

	/**
	 * Re-opens a claim and redirects back to the encounter view
	 *
	 * @param int
	 */
	function processReopen_edit($encounter_id) {
		$encounter =& Celini::newORDO('Encounter', $encounter_id);
		$encounter->set('status', 'open');
		$encounter->persist();
		
		// return display
		$this->_state = false;
		return $this->actionEdit($encounter->get('id'));
	}


	/**
	 * Rebill an claim
	 *
	 * This will be called by {@link processEdit()}
	 *
	 * @param  int
	 * @access private
	 */
	function _handleRebill(&$encounter) {
		$this->_sendClaim($encounter, 'rebill');
		// no need to return, as processEdit() will fall back to actionEdit()
	}

	


	/**
	 * Util function to check if we can rebill
	 *
	 * rule is: EOB payment has been made, There is an outstanding Balance, there is a secondary payer
	 */
	function _canRebill($encounterId) {
		$claim =& ClearhealthClaim::fromEncounterId($encounterId);

		ORDataObject::factory_include('Payment');
		$payments = Payment::fromForeignId($claim->get('id'));

		// check for EOB payment
		if (count($payments) > 0)  {

			$encounter =& Celini::newORDO('Encounter',$encounterId);

			// check for an outstanding balance
			$status = $claim->accountStatus($encounter->get('patient_id'),$encounterId);
			if ($status['total_balance'] > 0) {

				// If we're using a payer group and aren't currently on the last payer...
				if($encounter->get('payer_group') > 0) {
					$pg =& Celini::newORDO('PayerGroup',$encounter->get('payer_group'));
					$payers = $encounter->valueList('current_payers');
					if($encounter->get('current_payer') < 1 && count($payers) > 0) {
						return true;
					}
					$lastpayer =& $payers[count($payers)];
					if($encounter->get('current_payer') != $lastpayer->get('id')) {
						return true;
					}
				}
				// check for a secondary payer
				// Not sure if we should use this anymore with groups
				/*
				ORDataObject::factory_include('InsuredRelationship');
				$payers = InsuredRelationship::fromPersonId($encounter->get('patient_id'));
				if (count($payers) > 1) {
					return true;
				}
				*/
			}
		}

		return false;
	}

	function update_action($foreign_id = 0, $coding_data_id = 0, $icd_id = 0) {
		$this->coding_data_id = $coding_data_id;
		$this->edit_icd_code = $icd_id;
		return $this->actionEdit($this->get('encounter_id'));
	}

	function _generateClaim(&$encounter,$claim = false) {
		$this->_sendClaim($encounter, 'new');
	}
	
	/**
	 * Handles the actual interaction with the gateway
	 *
	 * <i>$type</i> should always be "new" or "rebill" or "rebillnext"
	 *
	 * @param  Encounter
	 * @param  string
	 * @access private
	 */
	function _sendClaim(&$encounter, $type) {
		assert('$type == "new" || $type == "rebill"');
		// load gateway
		global $loader;
		$loader->requireOnce('includes/freebGateway/ClearhealthToFreebGateway.class.php');
		
		$gateway =& new ClearhealthToFreebGateway($this, $encounter);
		$gateway->send($type);
	}

	/**
	 * Deletes a claimline from the encounter
	 *
	 * This serves as an alias for {@link C_Coding::delete_claimline()}.
	 *
	 * @param  int
	 * @access protected
	 * @see    C_Coding::delete_claimline()
	 */
	function delete_claimline_action_process($claimline_id) {
		$encounter =& Celini::newORDO('Encounter', $this->GET->getTyped('encounter_id', 'int'));
		
		// double check to insure the encounter is open
		if($encounter->get('status') === "open") {
			$this->coding->delete_claimline($claimline_id);
		}
		
		// return display
		$this->_state = false;
		return $this->actionEdit($encounter->get('id'));
	}

	function actionRouteSlip_view($encounterId = false) {
		if ($encounterId == false) {
			$encounterId = $this->get('encounter_id');
		}

		if (!$encounterId) {
			die( "<p>An encounter must be selected to run the Route Slip report.</p>" );
		}

		$rs =& Celini::newOrdo('RouteSlip');
		$rs->set('encounter_id',$encounterId);
		$rs->persist();

		$this->view->assign('route_slip_id',$rs->get('id'));

		$e =& Celini::newOrdo('Encounter',$encounterId);
		$patient =& Celini::newOrdo('Patient',$e->get('patient_id'));
		$payer =& Celini::newOrdo('InsuranceProgram',$e->get('current_payer'));
		$practice =& Celini::newOrdo('Practice',$e->get('practice_id'));
		$address =& $patient->address();
		$provider =& Celini::newOrdo('Provider',$e->get('treating_person_id'));

		$ip =& Celini::newOrdo('InsuredRelationship',array($e->get('current_payer'),$e->get('patient_id')),'ByInsuranceProgramAndPerson');
		$this->view->assign('copay',$ip->get('copay'));

		$profile =& Celini::getCurrentUserProfile();
		$this->view->assign('user_id',$profile->getUserId());

		$ts = TimestampObject::create(date('Y-m-d H:i:s'));
		$this->view->assign('timestamp',$ts->toString());

		$this->view->assign_by_ref('encounter',$e);
		$this->view->assign_by_ref('patient',$patient);
		$this->view->assign_by_ref('address',$address);
		$this->view->assign_by_ref('payer',$payer);
		$this->view->assign_by_ref('practice',$practice);
		$this->view->assign_by_ref('provider',$provider);

		if (isset($this->noRender) && $this->noRender === true) {
			return "routeSlip.html";
		}
		return $this->view->render("routeSlip.html");

	}

	function actionDelete() {
		$eid = Enforcetype::int($this->getDefault('encounter_id'));
		$e =& Celini::newOrdo('Encounter',$eid);
			if ($e->isPopulated()) {
				return "<form id='del' method='post' action='".Celini::link('delete')."'><input type='hidden' name='encounter_id' value='$eid'><input type='hidden' name='process' value='true'></form><script type='text/javascript'>$('del').submit()</script>";
			}
			else {
				Celini::redirect('CalendarDisplay','day');
			}
		}

	function processDelete() {
		$eid = Enforcetype::int($this->POST->get('encounter_id'));
		$e =& Celini::newOrdo('Encounter',$eid);
		$e->drop();
	}
}
?>
