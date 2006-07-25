<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('controllers/C_SecondaryPractice.class.php');

/**
 * Controller Clearhealth Patient actions
 */
class C_Patient extends Controller {
	var $patient_id = 0;
	var $number_id = 0;
	var $address_id = 0;
	var $identifier_id = 0;
	var $insured_relationship_id = 0;
	var $person_person_id = 0;
	var $patient_statistics_id = 0;

	function _storeCurrentAction() {
		$current = $this->trail->current();
		if ($current->controller == 'Patient' && !strstr($current->link(),'minimal')) {
			$currentUrl = $current->link();
			if ($this->patient_id > 0 && preg_match('/(.*)\/patient\/edit\/update\/0\?$/i', $currentUrl, $matches)) {
				$currentUrl = $matches[1] . '/patient/edit/' . $this->patient_id;
			}
			$this->session->set('patient_action', $currentUrl);
		}
	}

	/**
	 * Handle displaying the add diaglog
	 *
	 * @see actionEdit()
	 */
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	/**
	 * Edit/Add an Patient
	 *
	 */
	function actionEdit($patient_id = 0) {
		$head =& Celini::HTMLHeadInstance();
		$head->addJs('quicksave','quicksave');
		$head->addExternalCss('suggest');
		$this->_storeCurrentAction ();

		$this->assign('lockTimestamp',time());
		if (isset($this->patient_id) && $this->patient_id >0) {
			$patient_id = $this->patient_id;
		}
		$GLOBALS['loader']->requireOnce('includes/QuickSave.class.php');
		$qs =& new QuickSave();
		if($qs->loadForm('patientGeneralEditForm',$patient_id) !== false) {
			$this->view->assign('formid','patientGeneralEditForm');
			$this->messages->addMessage($this->view->render('restoreform.html'));
		}

		$GLOBALS['C_MAIN']['noOverlib'] = true;

		// Setup action values
		$formAction = Celini::managerLink('update',$patient_id, 'edit');
		$this->assign('FORM_ACTION', $formAction);
		$this->assign('EDIT_NUMBER_ACTION',Celini::managerLink('editNumber',$patient_id));
		$this->assign('DELETE_NUMBER_ACTION',Celini::managerLink('deleteNumber',$patient_id));
		$this->assign('EDIT_ADDRESS_ACTION',Celini::managerLink('editAddress',$patient_id));
		$this->assign('DELETE_ADDRESS_ACTION',Celini::managerLink('deleteAddress',$patient_id));
		$this->assign('NEW_PAYER',Celini::managerLink('editInsuredRelationship',$patient_id)."id=0&&process=true");
		$this->assign('DUPLICATE_ACTION',Celini::link('markDuplicate',true,'minimal').'patient_id='.$patient_id);
		$this->assign('UNDUPLICATE_ACTION',Celini::managerLink('unmarkDuplicate',$patient_id));
		
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('clnipopup');

		$user =& ORdataObject::factory('User');
		$person =& ORdataObject::factory('Patient',$patient_id);
		$number =& ORDataObject::factory('PersonNumber',$this->number_id,$patient_id);
		$address =& ORDataObject::factory('PersonAddress',$this->address_id,$patient_id);
		$identifier =& ORDataObject::factory('Identifier',$this->identifier_id,$patient_id);
		
		if ($person->isPopulated()) {
			$this->set('patient_id',$person->get('id'));
			$this->set('external_id',$person->get('id'));
		}


		$nameHistoryGrid =& new cGrid($person->loadDatasource('NameHistoryList'));
		$nameHistoryGrid->name = "nameHistoryGrid";
		$identifierGrid =& new cGrid($person->identifierList());
		$identifierGrid->name = "identifierGrid";
		$identifierGrid->registerTemplate('identifier','<a href="'.Celini::ManagerLink('editIdentifier',$patient_id).'id={$identifier_id}&process=true">{$identifier}</a>');
		$identifierGrid->registerTemplate('actions','<a href="'.Celini::ManagerLink('deleteIdentifier',$patient_id).'id={$identifier_id}&process=true">delete</a>');
		$identifierGrid->setLabel('actions',false);

		$insuredRelationshipGrid =& new cGrid($person->loadDatasource('InsuredRelationshipList'));
		$insuredRelationshipGrid->name = "insuredRelationshipGrid";
		$insuredRelationshipGrid->registerTemplate('company','<a href="'.Celini::ManagerLink('editInsuredRelationship',$patient_id).'id={$insured_relationship_id}&process=true">{$company}</a>');
		$insuredRelationshipGrid->indexCol = false;

		$insuredRelationship =& ORDataObject::factory('InsuredRelationship',$this->insured_relationship_id,$patient_id);
		$this->payerCount = $insuredRelationship->numRelationships($patient_id);

		$subscriber =& ORDataObject::factory('Patient',$insuredRelationship->get('subscriber_id'));

		$insuranceProgram =& ORDataObject::Factory('InsuranceProgram');
		$this->assign_by_ref('insuranceProgram',$insuranceProgram);

		$personPerson =& ORDataObject::factory('PersonPerson',$this->person_person_id,$patient_id);
		$personPersonGrid = new cGrid($person->loadDatasource('RelatedList'));
		$personPersonGrid->name = "personPersonGrid";
		$personPersonGrid->registerTemplate('relation_type','<a href="'.Celini::ManagerLink('editPersonPerson',$patient_id).'id={$person_person_id}&process=true">{$relation_type}</a>');

		$building =& ORDataOBject::factory('Building');
		$encounter =& ORDataOBject::factory('Encounter');
		
		$patientStatistics =& ORDataObject::factory('PatientStatistics',$patient_id);

		$pcc =& Celini::newOrdo('PatientChronicCode');
		$chronicCodes = $pcc->patientCodeArray($patient_id,true);
		
		$relatedAddressList =& $person->loadDatasource('RelatedAddressList');
		$relatedAddressGrid =& new cGrid($relatedAddressList);
		$relatedAddressGrid->indexCol = false;
		$this->assign_by_ref('relatedAddressGrid', $relatedAddressGrid);
		
		
		// Generate view for SecondaryPractice
		$cSecondaryPractice =& new C_SecondaryPractice();
		$cSecondaryPractice->view->assign('FORM_ACTION', $formAction);
		$cSecondaryPractice->person =& $person;
		$this->view->assign('secondaryPracticeView', $cSecondaryPractice->actionDefault());

		
		$this->assign("providers_array",$this->utility_array($user->users_factory("provider"),"id","username"));
		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('building',$building);
		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('number',$number);
		$this->assign_by_ref('address',$address);
		$this->assign_by_ref('identifier',$identifier);
		$this->assign_by_ref('nameHistoryGrid',$nameHistoryGrid);
		$this->assign_by_ref('identifierGrid',$identifierGrid);
		$this->assign_by_ref('insuredRelationship',$insuredRelationship);
		$this->assign_by_ref('insuredRelationshipGrid',$insuredRelationshipGrid);
		$this->assign_by_ref('personPerson',$personPerson);
		$this->assign_by_ref('personPersonGrid',$personPersonGrid);
		$this->assign_by_ref('patientStatistics',$patientStatistics);
		$this->assign_by_ref('subscriber',$subscriber);
		$this->assign('hide_type',true);
		$this->assign('chronicCodes',$chronicCodes);

		$this->assign('now',date('Y-m-d'));

		if ($this->GET->exists('view') && $this->GET->get('view') === 'narrow') {
			return $this->view->render("singleColEdit.html");
		}
		return $this->view->render("edit.html");
	}

	/**
	 * List Patients
	 */
	function actionList_view() {
		$this->_storeCurrentAction ();
		
		$person =& ORDataObject::factory('Patient');

		$ds =& $person->patientList();
		$ds->template['name'] = "<a href='".Celini::link('view','PatientDashboard')."id={\$person_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);
		$grid->pageSize = 50;

		$this->assign_by_ref('grid',$grid);

		return $this->view->render("list.html");
	}

	function actionFamilyStatement_view($patientId = false) {
		$this->_storeCurrentAction ();
		
		return $this->actionStatement_view($patientId,true);
	}

	function _ordoSnap($name,&$ordo) {
		if (isset($this->data['ordo'][$name])) {
			$ordo->populateArray($this->data['ordo'][$name]);
		}
		else {
			$this->data['ordo'][$name] = $ordo->helper->persistToArray($ordo);
		}
	}

	function actionMarkDuplicate() {
		$patientId = EnforceType::int($this->getDefault('patient_id'));

		$patient =& Celini::newOrdo('Patient',$patientId);
		$this->view->assign_by_ref('patient',$patient);

		$this->view->assign('FORM_ACTION',Celini::link(true).'patient_id='.$patientId);

		return $this->view->render('markDuplicate.html');
	}

	function processMarkDuplicate() {
		$patientId = EnforceType::int($this->getDefault('patient_id'));

		$search = '';
		if ($this->POST->exists('search')) {
			$search = $this->POST->get('search');
		}

		$GLOBALS['loader']->requireOnce('controllers/C_PatientFinder.class.php');

		$finder = new C_PatientFinder();
		$results = $finder->SmartSearch($search);

		foreach($results as $key => $row) {
			if ($row['id'] == $patientId) {
				unset($results[$key]);
			}
		}
		$this->view->assign('results',$results);
		$this->view->assign('search',$search);
		$this->view->assign('MARK_ACTION',Celini::link('FinishMark'));
	}

	function actionFinishMark() {
		return $this->view->render('finishMark.html');
	}

	function processFinishMark() {
		$queue =& Celini::newOrdo('DuplicateQueue');
		$queue->populateArray($this->POST->get('DuplicateQueue'));
		$queue->persist();

		$patient =& Celini::newOrdo('Patient',$queue->get('child_id'));
		$patient->set('inactive',1);
		$patient->persist();
	}

	/**
	 * @todo figure out somewhere else to put all this sql, im not sure if a ds works with the derived tables, but maybe it does
	 */
	function actionStatement_view($patientId = false,$includeDependants=false) {
		$this->_storeCurrentAction ();
		$db =& Celini::dbInstance();

		if (!$patientId) {
			$patientId = $this->get('patient_id');
		}
		EnforceType::int($patientId);
		
		$reportId = $this->GET->get('report_id');
		if (!$reportId) {
			$reportId = $_GET[0];
		}
		$r =& Celini::newOrdo('Report',$reportId);
		$snapshotId = false;
		if (($this->GET->get('snapshot') == 'true' || $r->get('snapshot_style') == 1) && !$this->GET->exists('snapshotId')) {
			$rs =& Celini::newOrdo('ReportSnapshot');
			$rs->persist();
			$this->view->rs =& $rs;
			$this->data = array();
			$this->data['ordo'] = array();
		}
		else if ($this->GET->exists('snapshotId')) {
			$rs =& Celini::newOrdo('ReportSnapshot',$this->GET->get('snapshotId'));
			$this->data = unserialize($rs->get('data'));
			$snapshotId = true;
		}

		$p =& Celini::newOrdo('Patient',$patientId);
		$this->_ordoSnap('Patient',$p);
		$this->view->assign_by_ref('patient',$p);

		$g = $p->get('guarantor');
		$this->_ordoSnap('Guarantor',$g);
		if ($g->isPopulated()) {
			$this->view->assign_by_ref('guarantor',$g);
			$this->view->assign_by_ref('guarantorAddress',$g->address());
		}
		else {
			$this->view->assign_by_ref('guarantor',$p);
			$this->view->assign_by_ref('guarantorAddress',$p->address());
		}
		$this->_ordoSnap('Guarantor',$g);

		
		$pro = $p->get('defaultProviderPerson');
		$this->_ordoSnap('defaultProviderPerson',$pro);
		$this->view->assign_by_ref('provider',$pro);

		$practice = $p->get('defaultPractice');
		$this->_ordoSnap('defaultPractice',$practice);
		$this->view->assign_by_ref('practice',$practice);

		$practiceAddress = $practice->get('billingAddress');
		$this->_ordoSnap('practiceAddress',$practiceAddress);
		$this->view->assign_by_ref('practiceAddress',$practiceAddress);


		$sh =& Celini::newOrdo('StatementHistory');
		$this->_ordoSnap('StatementHistory',$sh);
		$sh->set('patient_id',$patientId);
		$sh->set('type',1); // 1 is for print 2 is for preview
		if (isset($rs)) {
			$sh->set('report_snapshot_id',$rs->get('id'));
		}

		if (!$snapshotId) {
			$sh->persist();
		}

		$this->assign('statement_date',$sh->get('date_generated'));
		$this->assign('statement_number',$sh->get('statement_number'));
		$this->assign('pay_by',$sh->get('pay_by'));

		list($sql,$agingSql) = $this->_genPatientStatementSql($patientId,$includeDependants);	
		list($lines,$plines) = $this->_statementData($patientId,$includeDependants,$snapshotId,$sh,$sql,$agingSql);

		if (isset($rs)) {
			$rs->set('data',serialize($this->data));
		}

		if (isset($this->noRender) && $this->noRender === true) {
			return "statement.html";
		}
		return $this->view->render("statement.html");
	}
	
	function _statementData($patientId,$includeDependants,$snapshotId,&$sh,$sql,$agingSql) {
		$aging = array(
			0=>0,
			30=>0,
			60=>0,
			90=>0,
			120=>0,
			150=>0
		);


		$db =& Celini::dbInstance();
		$res = $db->execute($sql);

		$lines = array();
		$total_charges = 0;
		$total_credits = 0;
		$total_outstanding = 0;
		while($res && !$res->EOF) {
			//var_dump($res->fields);
			$total_charges += $res->fields['charge'];
			$total_credits += $res->fields['credit'];
			$res->fields['outstanding'] = number_format($total_charges - $total_credits,2);
			$lines[] = $res->fields;
			$res->MoveNext();
		}
		$p =& Celini::newOrdo('Patient',$patientId);
		
		$ppplan =& Celini::newORDO('PatientPaymentPlan');
		if($includeDependants == true) {
			$persons = array($patientId)+$p->get_guarantees();
		} else {
			$persons = array($patientId);
		}
		$plines = array();
		$pinfo = array('balance'=>0,'total'=>0,'payments'=>0);
		$planaging = array(0=>0,30=>0,60=>0,90=>0,120=>0);
		$paybyts = strtotime($sh->get('pay_by'));
		$ts30 = strtotime('-30 days',$paybyts);
		$ts60 = strtotime('-60 days',$paybyts);
		$ts90 = strtotime('-90 days',$paybyts);
		$ts120 = strtotime('-120 days',$paybyts);
		
		foreach($persons as $person) {
			$plans = $ppplan->getByPatient($person);
			$person =& Celini::newORDO('Person',$person);
			foreach($plans as $plan) {
				$payments = $plan->get_unpaid_payments(date('Y-m-d',$paybyts));
				foreach($payments as $payment) {
					$paymentts = strtotime($payment->get('payment_date'));
					if($paymentts <= $paybyts) {
						$planaging[0] += $payment->get_pending_amount();
					}
					if($paymentts <= $ts30) {
						$planaging[30] += $payment->get_pending_amount();
					}
					if($paymentts <= $ts60) {
						$planaging[60] += $payment->get_pending_amount();
					}
					if($paymentts <= $ts90) {
						$planaging[90] += $payment->get_pending_amount();
					}
					if($paymentts <= $ts120) {
						$planaging[120] += $payment->get_pending_amount();
					}
					$plines[] = array('item_date'=>$payment->get('payment_date'),'person'=>$person->get('last_name').', '.$person->get('first_ame'),'code_text'=>'Payment Plan','charge'=>sprintf('%.2f',$payment->get('amount')),'credit'=>sprintf('%.2f',$payment->get('paid_amount')),'outstanding'=>$payment->get_pending_amount());
					$pinfo['balance'] += $payment->get_pending_amount();
					$pinfo['total'] += $payment->get('amount');
					$pinfo['payments'] += $payment->get('paid_amount');
				}
			}
		}
		if(count($plines) > 0) {
			$this->view->assign('plans',true);
		}
		$this->view->assign('plines',$plines);
		$this->view->assign('pinfo',$pinfo);
		
		if ($snapshotId && isset($this->data['ordo']['lines'])) {
			$lines = $this->data['ordo']['lines'];
		}
		else {
			$this->data['ordo']['lines'] = $lines;
		}

		if ($snapshotId && isset($this->data['ordo']['balance'])) {
			$total_charges = $this->data['ordo']['balance']['total_charges'];
			$total_credits = $this->data['ordo']['balance']['total_credits'];
			$total_outstanding = $this->data['ordo']['balance']['total_outstanding'];
		}
		else {
			$this->data['ordo']['balance']['total_charges'] = $total_charges;
			$this->data['ordo']['balance']['total_credits'] = $total_credits;
			$this->data['ordo']['balance']['total_outstanding'] = $total_outstanding;
		}
		$this->assign('lines',$lines);

		$balance = $total_charges-$total_credits;

		$this->assign('total_charges',$total_charges);
		$this->assign('total_credits',$total_credits);
		$this->assign('total_outstanding',$balance);
		
		$this->assign('total_account_balance',number_format($balance,2));
		$this->assign('insurance_pending',number_format(0,2));
		if(count($plines) > 0) {
			$this->assign('current_balance_due',number_format($pinfo['balance'],2));
		} else {
			$this->assign('current_balance_due',number_format($balance,2));
		}

		$sh->set('amount',$balance);
		$sh->persist();

		$res = $db->execute($agingSql);
		while($res && !$res->EOF) {
			$aging[$res->fields['period']] += $res->fields['balance'];
			$res->MoveNext();
		}
		
		if(count($plines) > 0) {
			$aging = $planaging;
		}
		
		if ($snapshotId && isset($this->data['ordo']['aging'])) {
			$aging = $this->data['ordo']['aging'];
		}
		else {
			$this->data['ordo']['aging'] = $aging;
		}

		$this->assign('aging',$aging);

		return array($lines,$plines);
	}

	function actionGuarantor_view() {
		$includeDependants=true;		
		$this->_storeCurrentAction ();
		$db =& Celini::dbInstance();
		if($this->GET->exists('withbalance')) {
			$withbalance = true;
		} else {
			$withbalance = false;
		}
		$this->view->assign('withbalance',$withbalance);
		
		// Get list of guarantors
		$sql = "SELECT DISTINCT related_person_id from person_person WHERE guarantor = 1";
		$gres = $db->execute($sql);
		$guarantorarray = array();
		for($gres->MoveFirst();!$gres->EOF;$gres->MoveNext()) {
			$patientId = $gres->fields['related_person_id'];
			$p =& Celini::newOrdo('Patient',$patientId);
			$this->view->assign_by_ref('patient',$p);

			list($sql,$agingSql) = $this->_genPatientStatementSql($patientId,$includeDependants,$withbalance);
			$res = $db->execute($sql);
			$lines = array();
			$total_charges = 0;
			$total_credits = 0;
			$total_outstanding = 0;
			while($res && !$res->EOF) {
				//var_dump($res->fields);
				$total_charges += $res->fields['charge'];
				$total_credits += $res->fields['credit'];
				$res->fields['outstanding'] = number_format($total_charges - $total_credits,2);
				$lines[] = $res->fields;
				$res->MoveNext();
			}

			$this->data['ordo']['balance']['total_charges'] = $total_charges;
			$this->data['ordo']['balance']['total_credits'] = $total_credits;
			$this->data['ordo']['balance']['total_outstanding'] = $total_outstanding;
			if(($withbalance==true && $total_credits - $total_charges != 0) || !$withbalance) {
				$guarantorarray[] = array('last_name'=>'<a href="'.Celini::link('view','PatientDashboard').'id='.$p->get('id').'">'.$p->get('last_name').'</a>','first_name'=>$p->get('first_name'),'balance'=>sprintf('%.2f',$total_charges-$total_credits));
			}
		}
		$GLOBALS['loader']->requireOnce("includes/Datasource_array.class.php");
		$ds =& new Datasource_array();
		$ds->setup(array('last_name'=>'Last Name','first_name'=>'First Name','balance'=>'Balance'),$guarantorarray);
		$grid =& new cGrid($ds);
		$grid->indexCol = false;
		$this->view->assign_by_ref('grid',$grid);
		return "guarantorreport.html";
	}

	function _genPatientStatementSql($patientId,$includeDependants,$withbalance=true,$filters=array()) {
		$format = DateObject::getFormat();

		$patientSelectSql = "
		e.patient_id = $patientId
		";
		$this->assign('familyStatement',false);
		if ($includeDependants) {
			$patientSelectSql = "(e.patient_id =$patientId or e.patient_id 
				in(select person_id from person_person where related_person_id = $patientId and guarantor = 1))";
			$this->assign('familyStatement',true);
		}

		$withbalance = $withbalance == true ? 'AND balance > 0' : '';
		$encounterBalanceSql = "
		select
			feeData.encounter_id,
			(charge - ifnull(credit,0.00)) balance
		from
			/* Fee total */
			(
			select
				e.encounter_id,
				sum(cd.fee) charge
			from
				encounter e
				inner join clearhealth_claim cc using(encounter_id)
				inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
			where
				$patientSelectSql
			group by
				e.encounter_id
			) feeData
		left join
			/* Payment totals */
			(
			select
				e.encounter_id,
				(sum(pl.paid) + sum(pl.writeoff)) credit
			from
				encounter e
				inner join clearhealth_claim cc using(encounter_id)
				inner join payment p on cc.claim_id = p.foreign_id
				inner join payment_claimline pl on p.payment_id = pl.payment_id
			where
				$patientSelectSql
			group by
				e.encounter_id
			) paymentData on feeData.encounter_id = paymentData.encounter_id
		WHERE feeData.encounter_id NOT IN (
			select e.encounter_id FROM encounter AS e
			INNER JOIN relationship EPPP ON EPPP.parent_type = 'Encounter' AND EPPP.parent_id=e.encounter_id AND EPPP.child_type='PatientPaymentPlan'
			INNER JOIN patient_payment_plan ppp ON ppp.patient_payment_plan_id=EPPP.child_id AND ppp.balance > 0
			)
		";

		$agingSql = "
		select
			e.encounter_id,
			CASE 
				WHEN (TO_DAYS(now()) - TO_DAYS(e.date_of_treatment)) < 30 THEN 0 
				WHEN (TO_DAYS(now()) - TO_DAYS(e.date_of_treatment)) < 60 THEN 30 
				WHEN (TO_DAYS(now()) - TO_DAYS(e.date_of_treatment)) < 90 THEN 60 
				WHEN (TO_DAYS(now()) - TO_DAYS(e.date_of_treatment)) < 120 THEN 90 
				ELSE 120
			END period,
			balance
		from
			encounter e
			inner join ($encounterBalanceSql) eb using(encounter_id)
		";


		// charges from claimlines
		$sql = "
		select * from (
		select
			date_format(e.date_of_treatment,'$format') item_date,
			c.code_text,
			c.code,
			cc.total_billed charge,
			0.00 credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
			inner join codes c using(code_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql and balance > 0
		";
		// payments from co-pays
		$sql .= "
		union
		select
			date_format(p.payment_date,'$format') item_date,
			'Co-Pay' code_text,
			'' code,
			0.00 charge,
			(pl.paid+pl.writeoff) credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join payment p on e.encounter_id = p.encounter_id
			inner join payment_claimline pl using(payment_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql
			and balance > 0
		";

		// payments to claimlines
		$sql .= "
		union
		select
			date_format(p.payment_date,'$format') item_date,
			c.code_text,
			c.code,
			0 charge,
			(pl.paid+pl.writeoff) credit,
			0.00,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join payment p on cc.claim_id = p.foreign_id
			inner join payment_claimline pl using(payment_id)
			inner join codes c using(code_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql
			and p.encounter_id = 0
			$withbalance
		) data
		order by encounter_id DESC , item_date
		";
		return array($sql,$agingSql);
	}
	
	function actionBalanceReport_view() {
		$GLOBALS['loader']->requireOnce("datasources/PatientBalance_DS.class.php");
		$ds =& new PatientBalance_DS($_GET);
		$grid =& new cGrid($ds);
		$this->view->assign_by_ref('grid',$grid);
		$prac =& Celini::newORDO('Practice');
		$prov =& Celini::newORDO('Provider');
		$this->view->assign_by_ref('provider',$prov);
		$this->view->assign_by_ref('practice',$prac);
		$ses =& Celini::sessionInstance();
		$filters = $ses->get('balancereport:filters');
		$this->view->assign('filters',$filters);
		$this->view->assign('balanceoptions',array('credit'=>'Credit Only','balance'=>'With Balance Only'));
		$this->view->assign('reportid',$this->GET->getTyped('report_id','int'));
		if (isset($this->noRender) && $this->noRender === true) {
			return "balancereport.html";
		}
		return $this->view->render('balancereport.html');
	}
	
	function actionStatementReport_view($patientId=false,$includedependants=false) {
		if (!$patientId) {
			$patientId = $this->get('patient_id');
		}
		EnforceType::int($patientId);

		$GLOBALS['loader']->requireOnce('datasources/Patient_Statement_DS.class.php');
		$ds =& new Patient_Statement_DS($patientId,true,true);
		$grid =& new cGrid($ds);
		$this->view->assign_by_ref('grid',$grid);
		if (isset($this->noRender) && $this->noRender === true) {
			return "statementreport.html";
		}
		return $this->view->render('statementreport.html');
	}
	
}
?>