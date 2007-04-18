<?php
$loader->requireOnce('controllers/C_FreeBGateway.class.php');
$loader->requireOnce('includes/clni/clniAudit.class.php');
	
class C_Queue extends controller {

	function C_Queue() {
		parent::Controller();


		$queues = $this->_getQueues();
		$this->assign('queues',$queues);

		$this->assign('EDIT_ACTION',Celini::link('edit','Queue','main'));
		$this->assign('PROCESS_ACTION',Celini::link('process','Queue','main'));
		$this->assign("QUEUE_ACTION", Celini::link('replace','Queue',false));
		$this->assign("HISTORY_ACTION", Celini::link('history','Queue','main'));
	}

	function actionView() {
		$queues = $this->_getQueues();
		$this->assign('queues',$queues);

		if ($this->GET->exists('ajax')) {
			$this->view->assign('ajaxFlag',$this->GET->get('ajax'));
		}

		return $this->view->render('view.html');
	}

	function actionProcess_edit() {
		$ajax =& Celini::ajaxInstance();
		$ajax->jsLibraries[] = array('billingList');

		$fbg = new C_FreeBGateway();
		$this->assign_by_ref("fbg",$fbg);

		$queues = $this->_getQueues();
		$this->assign('queues',$queues);
		$this->assign("QUEUE_ACTION", Celini::link('process','Queue','main'));
		return $this->view->render('process.html');
	}

	function processProcess_edit() {
		// for BC
		$_POST['target'] = 'txt';
		
		$results = "";

		$ids = $this->_getQueueMembers($this->POST->get('queueId'));

		$qid = $this->POST->getTyped('queueId','int');
		$q =& Celini::newOrdo('FBQueue',$qid);
		$audit =& new clniAudit();

		$numItem = count($ids);

		$fbg = new C_FreeBGateway();
		$cv = $fbg->claimVariationList();
		$cd = $fbg->claimDestinationList();


		$variation = $cv[$this->POST->get('variation')];
		$destination = $cd[$this->POST->get('destination')];

		$audit->logOrdo($q,'process',"Processed Queue with $numItem claims<br>Variation: $variation<br>Destination:$destination");

		$batch = array();
		foreach($ids as $id) {
			$batch[$id]['on'] = 1;
		}

		$fbg = new C_FreeBGateway();
		$results = $fbg->claimResult_action_view($batch,$_POST['variation'],$_POST['target'],$_POST['destination']);
   
 		if ($results === false) {
			//$message = $fbg->claimLastError($c->get("claim_identifier"));
			$this->messages->addMessage("Error processing Queue");
			header('Content-type: text/html');
			header('Content-Disposition:');
		}

		if (count($this->messages->getMessages()) == 0) {
			$this->_continue_processing = false;
			$this->_state = false;

		//if the target is a pdf we need to create that now... 		
			if ($_POST['target'] === "pdf") {
				$GLOBALS['loader']->requireOnce('controllers/C_PDF.class.php');
				$cpdf = new C_PDF();
				$cpdf->display($results,false);
				exit;
			}

			$this->_clearQueue($this->POST->getTyped('queueId', 'int'));
		}

		return $results;
	}

	function processViewQueue() {
		$qid = $this->POST->getTyped('queueId','int');
		header('Location: '.Celini::link('list','Claim').'queue='.$qid);
		exit();
	}

	function actionAdd() {
		return $this->actionView();
	}

	function processAdd() {
		$queues = $this->_getQueues();
		$add = $this->POST->get('add');
		$qid = $this->POST->getTyped('queueId','int');

		if (!is_array($add)) {
			return;
		}

		$q =& Celini::newOrdo('FBQueue',$qid);
		$ids = $q->get('ids');
		$ni = $q->get('num_items');
		$mi = $q->get('max_items');

		foreach($add as $id) {
			if ($ni == $mi) {
				break;
			}
			if (!array_key_exists($id, $ids)) {
				$ni++;
				$ids[$id] = $id;
			}
		}
		$q->set('num_items',$ni);
		$q->set('ids',$ids);
		$q->persist();
	}

	function actionClear() {
		return $this->actionView();
	}

	function processClear() {
		$this->_clearQueue($this->POST->getTyped('queueId','int'));
	}

	function actionEdit($id = 0) {
		$this->assign('FORM_ACTION',Celini::link('edit','Queue',true,$id));
		$this->assign('EDIT_ACTION',Celini::link('edit','Queue'));
		$this->assign('DELETE_ACTION',Celini::link('delete','Queue'));

		$queues = $this->_getQueues();
		$this->view->assign('queues',$queues);

		if (isset($queues[$id])) {
			$this->view->assign('selectedQueue',$queues[$id]);
		}

		return $this->view->render('edit.html');
	}

	function processEdit($id = 0) {
		$q =& Celini::newOrdo('FBQueue',$id);
		$q->populateArray($this->POST->get('queue'));
		$q->persist();
	}

	function _defaultQueue() {
		return array(
			'id' => 1,
			'name' => 'Default',
			'maxItems' => 100,
			'numItems' => 0,	
		);
	}

	function processDelete($id) {
		$q =& Celini::newOrdo('FBQueue',$id);
		$q->drop();
	}

	function actionDelete() {
		return $this->actionEdit();
	}

	function actionHistory($queueId=false) {
		$queues = $this->_getQueues();
		if ($queueId) {
			$this->view->assign('queueId',$queueId);
			$this->view->assign('queueName',$queues[$queueId]['name']);
			$this->view->assign('history',$this->actionHistoryView($queueId));
		}
		$this->view->assign('queues',$queues);
		return $this->view->render('history.html');
	}

	function actionHistoryView($historyId) {
		$GLOBALS['loader']->requireOnce('datasources/QueueHistory_DS.class.php');

		$ds =& new QueueHistory_DS($historyId);
		$ds->setLabel('action','Action');
		$ds->registerTemplate('action','<a href="'.Celini::link('eob').'id={$audit_log_id}">Process EOB</a><br><a href="'.Celini::link('list','Claim','main').'history_id={$audit_log_id}">View Claims</a>');
		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);
		return $this->view->render('history_view.html');
	}

	function actionEob($auditLogId) {
		$audit = new clniAudit();
		$ids = unserialize($audit->oldFieldFromLogEntry($auditLogId,'ids'));
		$this->_setupEob($ids);
	}

	function actionEobQueue($queueId) {
		$ids = $this->_getQueueMembers($queueId);
		$this->_setupEob($ids);
	}

	function _setupEob($ids) {
		// we have a list of claim ids, were going to make an object tree 
		$GLOBALS['loader']->requireOnce('includes/X12Transaction.class.php');
		$transactions = array();

		foreach($ids as $id) {
			$fbc =& Celini::newOrdo('FBClaim',$id);
			//var_dump($fbc->toArray());

			$t = new X12Transaction();
			$t->fakeX12 = true;
			$t->summary->set('transactionType','C');
			$t->summary->set('identifier',$fbc->get('claim_identifier'));
			$t->summary->set('date',$fbc->get('date_sent'));

			// claim data
			$c = new X12Transaction_Claim();
			$c->fakeX12 = true;
			$c->set('identifier',$fbc->get('id'));
			$c->set('reference',$fbc->get('id'));
			$c->set('status',1);
			$c->set('claimPaymentAmount',0.00);
			$c->set('claimDate',$fbc->get('date_sent'));

			// claim lines
			$lines =& $fbc->childEntities('FBClaimline');
			$tc = 0;
			foreach($lines as $line) {
				$l = new X12Transaction_Claimline();
				$l->set('chargeAmount',$line->get('amount'));
				$tc += $line->get('amount');
				$l->set('paymentAmount',0.00);
				$c->lines[] = $l;

				//var_dump($line->toString());
			}

			// more claim stuff
			//$c->set('totalChargeAmount',$tc);

			// patient
			$fbp =& $fbc->childEntity('FBPatient');
			$p = new X12Transaction_Person();
			$p->set('nameFirst',$fbp->get('first_name'));
			$p->set('nameLast',$fbp->get('last_name'));
			$p->set('nameMiddle',$fbp->get('middle_name'));
			$p->set('id',$fbp->get('identifier'));
			$p->set('idType',$fbp->get('identifier_type'));

			$c->patient = $p;
			$t->details[] = $c;


			// payer
			$fbpy =& $fbc->childEntity('FBPayer');
			$p = new X12Transaction_Payer();
			$p->set('name',$fbpy->get('name'));
			$p->set('contactInfo',$fbpy->get('phone_number'));
			$p->set('contactInfoType','TE');

			$t->payer = $p;

			$transactions[] = $t;
		}

		$_SESSION['X12Import']['transactions'][2] = serialize($transactions);
		Celini::redirect('X12Apply','view',array('dataId' => 2));
	}

	/**#@+
	 * @access private
	 */
	function _getQueues() {
		$q =& Celini::newOrdo('FBQueue');
		$ret = $q->getQueueArray();
		if (count($ret) == 0) {
			$q->set('name','Default');
			$q->set('max_items',100);
			$q->set('num_items',0);
			$q->persist();
			$ret = $q->getQueueArray();
		}
		return $ret;
	}

	function _getQueueMembers($queueId) {
		$q = $this->_getQueues();
		return $q[$queueId]['ids'];
	}
	
	function _clearQueue($qid) {
		$q =& Celini::newOrdo('FBQueue',$qid);
		$q->set('num_items',0);
		$q->set('ids',array());
		$q->persist();
	}
	/**#@-*/
}
?>
