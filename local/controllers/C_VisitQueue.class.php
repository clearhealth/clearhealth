<?php

class C_VisitQueue extends Controller 
{
	var $template = null;
	var $queue = null;

	function actionList() {
		if($this->GET->get('provider_id') > 0) {
			$provider =& Celini::newORDO('Provider',$this->GET->get('provider_id'));
		} else {
			$provider =& Celini::newORDO('Provider');
		}
		$queues =& $provider->getChildren('VisitQueue');
		$this->view->assign_by_ref('queues',$queues);
		$this->view->assign_by_ref('provider',$provider);
		$this->view->render('list.html');
	}
	
	function actionListTemplates() {
		// big hack
		if ($this->GET->exists('delete_id')) {
			$t =& Celini::newORDO('VisitQueueTemplate',$this->GET->getTyped('delete_id','int'));
			$t->drop();
		}
		$template =& Celini::newORDO('VisitQueueTemplate');
		$templates = $template->valueList('title');
		$this->view->assign('templates',$templates);

		return $this->view->render('templatelist.html');
	}

	/**
	 * Function for editing the queue template
	 *
	 */
	function actionSetup() {
		if(!is_null($this->template)) {
			$template =& $this->template;
		} else {
			$template =& Celini::newORDO('VisitQueueTemplate',$this->GET->get('template_id'));
		}
		$this->view->assign_by_ref('template',$template);
		if($template->get('id') > 0) {
			$reasons =& $template->getReasons();
			$this->view->assign_by_ref('reasons',$reasons);
		}
		return $this->view->render('setup.html');
	}
	
	function actionEdit() {
		if(!is_null($this->queue)) {
			$queue =& $this->queue;
		} elseif($this->GET->get('patient_id') > 0 && $this->GET->get('provider_id') > 0) {
			$queue =& Celini::newORDO('VisitQueue');
			$queue->set('provider_id',$this->GET->get('provider_id'));
			$patient =& Celini::newORDO('Patient',$this->GET->get('patient_id'));
		} else {
			$queue =& Celini::newORDO('VisitQueue',$this->GET->get('queue_id'));
		}
		if (!isset($patient)) {
			$patient =& Celini::newORDO('Patient');
		}

		$this->view->assign_by_ref('patient',$patient);
		$template =& $queue->getTemplate();
		$this->view->assign_by_ref('queue',$queue);
		$this->view->assign_by_ref('template',$template);
		if($queue->get('id') > 0) {
			$patient =& $queue->getChild('Patient');
			$patient->populate();
			$this->view->assign_by_ref('patient',$patient);
			$appointments =& $queue->getChildren('Appointment');
			$this->view->assign_by_ref('appointments',$appointments);
		}
		$provider =& $queue->getProvider();
		$this->view->assign_by_ref('provider',$provider);
		return $this->view->render('edit.html');
	}
	
	function processEdit() {
		$qarray = $this->POST->getRaw('VisitQueue');
		if($qarray['id'] > 0) {
			$queue =& Celini::newORDO('VisitQueue',$qarray['id']);
			$this->queue =& $queue;
			$queue->populateArray($qarray);
			$queue->persist();
		} else {
			$queue =& Celini::newORDO('VisitQueue');
			$this->queue =& $queue;
			$queue->populateArray($qarray);
		}
		$template =& $queue->getTemplate();
		$provider =& $queue->getProvider();
		$this->messages->addMessage('Queue Updated');
	}
	
	function processSetup() {
		$tarray = $this->POST->getRaw('Template');
		$template =& Celini::newORDO('VisitQueueTemplate',$tarray['id']);
		$template->populateArray($tarray);
		$template->persist();
		$this->template =& $template;
		$reasons = $this->POST->getRaw('Reason');
		if(is_array($reasons)) {
			foreach($reasons as $rarray) {
				$reason =& Celini::newORDO('VisitQueueReason',$rarray['id']);
				$reason->populateArray($rarray);
				$reason->persist();
				$template->setChild($reason);
			}
			$rcount = $tarray['number_of_appointments'];
			$rs =& $template->getReasons();
			if($rcount < $rs->count()) {
				$rnum = 1;
				while($rs->valid()) {
					$r =& $rs->current();
					if($rnum > $rcount) {
						$r->drop();
					}
					$rnum++;
					$rs->next();
				}
			}
		}
		$this->messages->addMessage('Template Updated');
	}
	
	function actionDelete() {
		$qid = $this->GET->get('queue_id');
		if($qid > 0) {
			$q =& Celini::newORDO('VisitQueue',$qid);
			$q->drop();
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
}
