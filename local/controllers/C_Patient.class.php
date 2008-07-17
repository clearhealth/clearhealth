<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('controllers/C_SecondaryPractice.class.php');
$loader->requireOnce('ordo/Document.class.php');
$loader->requireOnce('includes/clni/clniAudit.class.php');

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

	function ajaxAuditAccess($patientId) {
		$patientId = (int)$patientId;
		$pat = ORDataObject::factory('Patient',$patientId);
		ClniAudit::logOrdo($pat,1,'Override Access');
		$_SESSION['confidentiality'][$patientId] = true;

	}

	function ajaxEditAddress($addressId) {
		return $this->actionEditAddress_edit($addressId);
	}

	function actionEditAddress_edit($addressId = 0) {
		$address = ORDataObject::factory('Address',$addressId);
		$this->assign('address',$address);
		return $this->view->render('addressPopup.html');
	}
	function ajaxEditNumber($numberId) {
		return $this->actionEditNumber_edit($numberId);
	}

	function actionEditNumber_edit($numberId = 0) {
		$number = ORDataObject::factory('Number',$numberId);
		$this->assign('number',$number);
		return $this->view->render('numberPopup.html');
	}
	function ajaxEditInsurer($insurerId) {
		return $this->actionEditInsurer_edit($insurerId);
	}

	function actionEditInsurer_edit($insurerId = 0) {
		$patientId = $this->get("patient_id");
		$ir = ORDataObject::factory('InsuredRelationship',$insurerId,$patientId);
		$this->assign('insuredRelationship',$ir);
		$subscriber =& ORDataObject::factory('Patient',$ir->get('subscriber_id'));
		$this->assign_by_ref('subscriber',$subscriber);
		$insuranceProgram =& ORDataObject::Factory('InsuranceProgram');
		$address =& ORDataObject::factory('PersonAddress',$this->address_id,$patientId);
		$this->view->assign_by_ref('address',$address);
		$this->assign_by_ref('insuranceProgram',$insuranceProgram);
		
		return $this->view->render('insurerPopup.html');
	}

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
	function actionAdd_add() {
		$this->patient_id = 0;
		$this->set("patient_id",0);
		return $this->actionEdit_edit(0);
	}
	
	/**
	 * Edit/Add an Patient
	 *
	 */
	function actionEdit_edit($patient_id = '') {
                if ($patient_id === '') {
			$patient_id = $this->get('patient_id', 'c_patient');
                }
		$head =& Celini::HTMLHeadInstance();
		$head->addJs('quicksave','quicksave');
		$head->addJs('scriptaculous');
		$head->addJs('ui');

		$head->addExternalCss('suggest');
		$this->_storeCurrentAction ();

		$em =& EnumManager::getInstance();
		$this->view->assign('em',$em);

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
		$this->assign('NEW_PAYER',Celini::managerLink('editInsuredRelationship',$patient_id)."id=0&process=true");
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
		$insuredRelationshipGrid->registerTemplate('company','<a href="javascript:insurerPopup({$insured_relationship_id});">{$company}</a>');
		$insuredRelationshipGrid->indexCol = false;

		$insuredRelationship =& ORDataObject::factory('InsuredRelationship',$this->insured_relationship_id,$patient_id);
		$this->payerCount = $insuredRelationship->numRelationships($patient_id);

		$subscriber =& ORDataObject::factory('Patient',$insuredRelationship->get('subscriber_id'));

		$insuranceProgram =& ORDataObject::Factory('InsuranceProgram');
		$this->assign_by_ref('insuranceProgram',$insuranceProgram);

		$parProgramGrid =& new cGrid($person->loadDatasource('ParticipationProgram'));
                $parProgramGrid->name = "parProgramGrid";
		$parProgramAddLink = Celini::link('AddConnect','ParticipationProgram',true,$patient_id);

		$personPerson =& ORDataObject::factory('PersonPerson',$this->person_person_id,$patient_id);
		$personPersonGrid = new cGrid($person->loadDatasource('RelatedList'));
		$personPersonGrid->name = "personPersonGrid";
		$personPersonGrid->registerTemplate('relation_type','<a href="'.Celini::ManagerLink('editPersonPerson',$patient_id).'id={$person_person_id}&process=true">{$relation_type}</a>');

		$building =& ORDataOBject::factory('Building');
		$encounter =& ORDataOBject::factory('Encounter');
		
		$patientStatistics =& ORDataObject::factory('PatientStatistics',$patient_id);

		
		$relatedAddressList =& $person->loadDatasource('RelatedAddressList');
		$relatedAddressGrid =& new cGrid($relatedAddressList);
		$relatedAddressGrid->indexCol = false;
		$this->view->assign_by_ref('relatedAddressGrid', $relatedAddressGrid);
		
		
		// Generate view for SecondaryPractice
		$cSecondaryPractice =& new C_SecondaryPractice();
		$cSecondaryPractice->view->assign('FORM_ACTION', $formAction);
		$cSecondaryPractice->person =& $person;
		$this->view->assign('secondaryPracticeView', $cSecondaryPractice->actionDefault());

		$practice =& Celini::newORDO('Practice');
		$this->view->assign_by_ref('practice',$practice);
		$this->view->assign("providers_array",$this->utility_array($user->users_factory("provider"),"id","username"));
		$this->view->assign_by_ref('person',$person);
		$this->view->assign_by_ref('provider',ORDataObject::factory('Person',$person->get('default_provider')));
		$this->view->assign_by_ref('building',$building);
		$this->view->assign_by_ref('encounter',$encounter);
		$this->view->assign_by_ref('number',$number);
		$this->view->assign_by_ref('address',$address);
		$this->view->assign_by_ref('identifier',$identifier);
		$this->view->assign_by_ref('nameHistoryGrid',$nameHistoryGrid);
		$this->view->assign_by_ref('identifierGrid',$identifierGrid);
		$this->view->assign_by_ref('insuredRelationship',$insuredRelationship);
		$this->view->assign_by_ref('insuredRelationshipGrid',$insuredRelationshipGrid);
		$this->view->assign_by_ref('parProgramGrid',$parProgramGrid);
		$this->view->assign_by_ref('parProgramAddLink',$parProgramAddLink);
		$this->view->assign_by_ref('personPerson',$personPerson);
		$this->view->assign_by_ref('personPersonGrid',$personPersonGrid);
		$this->view->assign_by_ref('patientStatistics',$patientStatistics);
		$this->view->assign_by_ref('subscriber',$subscriber);
		$this->view->assign('hide_type',true);

		$this->assign('now',date('Y-m-d'));

		$config = Celini::configInstance();
                $this->view->assign('customPatientStats',$config->get('customPatientStatistics',false));

		if ($this->GET->exists('view') && $this->GET->get('view') === 'narrow') {
			return $this->view->render("singleColEdit.html");
		}
		$config = Celini::configInstance()->get("PatientPicture");
                if (isset($config['enabled']) && $config['enabled'] == true) {
                	$width = $config['thumbWidth'];
                	$d = Document::FirstDocumentByCategoryName($patient_id,"Picture");
                	if (is_object($d)) {
                	        $pictureTag = '<img src="'.Celini::link("thumb","Thumbnail") . 'src=/' . $patient_id . "/" . $d->get("name") . '&w='. $width . '">';
                		$this->view->assign("pictureTag",$pictureTag);
                	}
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
	 * Creates a batch statement printout from multiple patients.
	 *
	 * @param string $patients
	 */
	function actionBatchStatement_view($patients) {
		$GLOBALS['loader']->requireOnce('controllers/C_Report.class.php');
		$c_report =& new C_Report();
		$patients = explode('-',$patients);
		$output = '';
		foreach($patients as $key=>$i) {
			if($key != 0) {
				$output .= '<DIV style="page-break-after:always"></DIV>';
			}
			$_GET['patient_id'] = $i;
			$this->set('patient_id',$i);
			$output .= $c_report->actionViewByCID('patient_statement');
		}
		return $output;
	}
	
	/**
	 * @todo figure out somewhere else to put all this sql, im not sure if a ds works with the derived tables, but maybe it does
	 */
	function actionStatement_view($patientId = false,$includeDependants=false) {
		$this->_storeCurrentAction ();
		$db = new clniDb();

		if (!$patientId) {
			$patientId = $this->get('patient_id');
		}
		EnforceType::int($patientId);
		
		$reportId = $this->GET->get('report_id');
		if ($this->GET->exists('cid')) {
			$cid = $this->GET->get('cid');
			$c = $db->quote($cid);
			$sql = "select id from reports where custom_id = $c";
			$reportId = $db->getOne($sql);
		}
		if (!$reportId) {
			if (isset($_GET[0])) {
				$reportId = $_GET[0];
			}
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


		$db =& new clniDB();
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
			$payer =& Celini::newORDO('InsuranceProgram',$res->fields['payer_id']);
			$res->fields['payer_name'] = $payer->value('fullname');
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

		// calc insurance pending
		$insurance_pending = $this->_calcInsurancePending($patientId,$includeDependants);

		$this->assign('total_charges',$total_charges);
		$this->assign('total_credits',$total_credits);
		$this->assign('total_outstanding',$balance);
		
		$this->assign('total_account_balance',number_format($balance,2));
		$this->assign('insurance_pending',number_format($insurance_pending,2));
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

	function _calcInsurancePending($patientId,$includeDependants) {
		$patientSelectSql = "e.patient_id =$patientId";
		if ($includeDependants) {
			$patientSelectSql = "(e.patient_id =$patientId or e.patient_id 
				in(select person_id from person_person where related_person_id = $patientId and guarantor = 1))";
		}

		// insurance pending is claims out to an insurance company that haven't been paid
		$sql = "
			select
			sum(fbcl.amount) - sum(paylines.writeoff) - sum(paylines.paid) amount
			from
				clearhealth_claim cc
				inner join encounter e on cc.encounter_id = e.encounter_id
				inner join fbclaim fbc on cc.identifier = fbc.claim_identifier
				inner join fblatest_revision fblr on fbc.claim_identifier = fblr.claim_identifier and fbc.revision = fblr.revision
				inner join fbclaimline fbcl on fbc.claim_id = fbcl.claim_id
				LEFT JOIN (
					SELECT
						e.patient_id,
						SUM(ifnull(writeoff,0)) AS writeoff,
						SUM(ifnull(amount,0)) AS paid
					FROM
						payment p
						inner join clearhealth_claim cc on p.foreign_id = cc.claim_id
						inner join encounter e on cc.encounter_id = e.encounter_id
					WHERE
						p.encounter_id = 0 and $patientSelectSql
					group by e.patient_id

				) AS paylines ON(paylines.patient_id = e.patient_id)
			where
				$patientSelectSql
				and fbc.claim_id in(
					select 
						fbc.claim_id
					from
						fbclaim fbc
						inner join fbcompany fbco on fbc.claim_id = fbco.claim_id and fbco.type = 'FBPayer'
					where
						fbco.name != 'System'
				)
			";
	

		$db = new clniDb();
		$amount = $db->getOne($sql);

		return $amount;
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

		$patientSelectSql = " e.patient_id = $patientId ";
		$this->assign('familyStatement',false);
		if ($includeDependants) {
			$patientSelectSql = "(e.patient_id =$patientId or e.patient_id 
				in(select person_id from person_person where related_person_id = $patientId and guarantor = 1))";
			$this->assign('familyStatement',true);
		}

		$withbalance = $withbalance == true ? 'AND balance > 0' : '';
		$encounterBalanceSql = "
			SELECT
				e.encounter_id,
				SUM(IFNULL(total_billed,0)) AS total_billed,
				SUM(IFNULL(total_paid,0)) AS total_paid,
				SUM(IFNULL(writeoffs.writeoff,0)) AS total_writeoff,
				SUM(IFNULL(total_billed,0)) - (SUM(IFNULL(total_paid,0)) 
					+ SUM(IFNULL(writeoffs.writeoff,0))) AS balance
			FROM
				encounter AS e
				LEFT JOIN clearhealth_claim AS cc on e.encounter_id = cc.encounter_id
				LEFT JOIN (
					SELECT
						foreign_id,
						SUM(ifnull(writeoff,0)) AS writeoff
					FROM
						payment p
						inner join clearhealth_claim cc on p.foreign_id = cc.claim_id
						inner join encounter e on cc.encounter_id = e.encounter_id
					WHERE
						p.encounter_id = 0 and $patientSelectSql
					GROUP BY
						foreign_id
				) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)
			where
				$patientSelectSql
				and e.encounter_id NOT IN (
					select e.encounter_id FROM encounter AS e
					INNER JOIN relationship EPPP ON EPPP.parent_type = 'Encounter' AND EPPP.parent_id=e.encounter_id AND EPPP.child_type='PatientPaymentPlan'
					INNER JOIN patient_payment_plan ppp ON ppp.patient_payment_plan_id=EPPP.child_id AND ppp.balance > 0
				)
			group by
				e.encounter_id
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
			cd.fee charge,
			0.00 credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name,
			prov.last_name AS provider_name,
			0 AS payer_id
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
			inner join codes c using(code_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
			left join person prov ON(e.treating_person_id=prov.person_id)
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql $withbalance
		";
		// misc charges
		$sql .= "
		union
		select
			date_format(e.date_of_treatment,'$format') item_date,
			CONCAT('Misc Charge: ',mc.title) code_text,
			'' code,
			mc.amount charge,
			0.00 credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name,
			prov.last_name AS provider_name,
			0 AS payer_id
		from
			encounter e
			inner join misc_charge mc on mc.encounter_id = e.encounter_id
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
			left join person prov ON(e.treating_person_id=prov.person_id)
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql $withbalance
		";
		// misc payments
		$sql .= "
		union
		select
			date_format(p.payment_date,'$format') item_date,
			'Misc Payment' code_text,
			'' code,
			0.00 charge,
			p.amount credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name,
			prov.last_name AS provider_name,
			p.payer_id
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join payment p on e.encounter_id = p.encounter_id
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
			left JOIN person prov ON(e.treating_person_id=prov.person_id)
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql
			$withbalance
			AND p.amount != 0
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
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name,
			prov.last_name provider_name,
			p.payer_id
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join payment p on cc.claim_id = p.foreign_id
			inner join payment_claimline pl using(payment_id)
			inner join codes c using(code_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
			left join person prov ON(e.treating_person_id=prov.person_id)
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql
			AND p.encounter_id = 0
			AND (pl.paid+pl.writeoff) != 0
			$withbalance
		) data
		order by encounter_id DESC , item_date ASC, charge DESC
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
