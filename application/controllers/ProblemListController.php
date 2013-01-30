<?php
/*****************************************************************************
*       ProblemListController.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


/**
 * Problem List controller
 */
class ProblemListController extends WebVista_Controller_Action {
	protected $_form;
	protected $_problem;
	protected $_status = array();

	public function init() {
		$this->_status = array();
		$this->_status[] = __('Active');
		$this->_status[] = __('Inactive');
		$this->_status[] = __('Removed');
		$this->_status[] = __('Resolved');
	}

	public function indexAction() {
		$this->render('index');
	}

	public function toolbarXmlAction() {
		header("Content-type: text/xml");
		$this->render('toolbar-xml');
	}

	public function filterJsonAction() {
		$tmp = array();
		$tmp['id'] = 'active_problems';
		$tmp['data'][] = __('Active Problems');
		$rows[] = $tmp;
		$tmp = array();
		$tmp['id'] = 'inactive_problems';
		$tmp['data'][] = __('Inactive Problems');
		$rows[] = $tmp;
		$tmp = array();
		$tmp['id'] = 'both_problems';
		$tmp['data'][] = __('Both Active & Inactive');
		$rows[] = $tmp;
		$tmp = array();
		$tmp['id'] = 'air_problems';
		$tmp['data'][] = __('Active, Inactive, & Resolved');
		$rows[] = $tmp;
		$tmp = array();
		$tmp['id'] = 'removed_problems';
		$tmp['data'][] = __('Removed Problems');
		$rows[] = $tmp;
		$tmp = array();
		$tmp['id'] = 'resolved_problems';
		$tmp['data'][] = __('Resolved Problems');
		$rows[] = $tmp;

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	function listMenuXmlAction() {
		header('Content-Type: application/xml;');
		$this->view->flags = Enumeration::getEnumArray('Problem List Flags');
		$this->render('list-menu-xml');
	}

	public function listJsonAction() {
		$personId = (int)$this->_getParam('personId');
		$filter = $this->_getParam('filter');
		// set default to active problem if filter is not specified
		if (strlen($filter) <= 0) {
			$filter = 'active_problems';
		}
		$filters = array();
		switch ($filter) {
			case 'active_problems':
				$filters['status'] = 'Active';
				break;
			case 'inactive_problems':
				$filters['status'] = 'Inactive';
				break;
			case 'both_problems':
				$filters['status'] = array('Active','Inactive');
				break;
			case 'air_problems':
				$filters['status'] = array('Active','Inactive','Resolved');
				break;
			case 'removed_problems':
				$filters['status'] = 'Removed';
				break;
			case 'resolved_problems':
				$filters['status'] = 'Resolved';
				break;
		}
		$filters['personId'] = $personId;
		$rows = array();
		$problemListIterator = new ProblemListIterator();
		$problemListIterator->setFilters($filters);
		foreach ($problemListIterator as $problem) {
			$comments = array();
			foreach ($problem->problemListComments as $comment) {
				$comments[] = $comment->comment . ' - ' . $comment->author->getDisplayName() . ' - ' . $comment->date;
			}
			$tmp = array();
			$tmp['id'] = $problem->problemListId;
			$tmp['data'][] = $problem->status;
			$tmp['data'][] = '<span style="font-size:6pt;font-style:italic;"><p>'.implode('</p><p>',$comments).'</p></span>';
			$tmp['data'][] = $problem->flags;
			$tmp['data'][] = $problem->codeTextShort;
			$tmp['data'][] = $problem->code;
			$tmp['data'][] = date('Y-m-d',strtotime($problem->dateOfOnset));
			$tmp['data'][] = $problem->provider->getOptionName();
			$tmp['data'][] = $problem->service;
			$tmp['data'][] = date('m/d/Y',strtotime($problem->lastUpdated));
			$rows[] = $tmp;
		}

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxEditProblemAction() {
		if (isset($this->_session->messages)) {
			$this->view->messages = $this->_session->messages;
		}
		$personId = (int)$this->_getParam('personId');
		$problemListId = (int)$this->_getParam('problemListId');
		$this->view->problemListId = $problemListId;
		$this->_form = new WebVista_Form(array('name' => 'editProblem'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "problem-list.raw/ajax-process-edit-problem");
		$this->_problem = new ProblemList();
		$this->_problem->problemListId = $problemListId;
		$this->_problem->personId = $personId;
		$this->_problem->populate();
		if (!$this->_problem->providerId > 0) {
			$auth = Zend_Auth::getInstance();
			$this->_problem->providerId = (int)$auth->getIdentity()->personId;
		}
		$this->_form->loadORM($this->_problem, "ProblemList");
		$this->_form->setWindow('windowEditProblem');
		$this->view->form = $this->_form;
		$this->view->problemList = $this->_problem;
		$this->view->services = $this->getServices();
		$this->render('ajax-edit-problem');
	}

	public function ajaxProcessEditProblemAction() {
		$this->ajaxEditProblemAction();
		$problemList = $this->_getParam('problemList');
		$id = $problemList['problemListId'];
		if (strlen($id) > 0) {
			$this->_problem->setProblemListId((int)$id);
			$this->_problem->populate();
		}
		$problemListComments = array();
		$comments = $this->_getParam('comments');
		if (count($comments) > 0) {
			$tmpComment = array();
			$tmpComment['authorId'] = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$tmpComment['date'] = date('Y-m-d');
			foreach ($comments as $comment) {
				$x = explode('_',$comment);
				unset($tmpComment['problemListCommentId']);
				if (substr($x[0],0,3) != 'plc') {
					$tmpComment['problemListCommentId'] = $x[0];
				}
				$tmpComment['comment'] = $x[1];
				$problemListComments[] = $tmpComment;
			}
		}
		$this->_problem->setProblemListComments($problemListComments);
		if (count($problemList) > 0) {
			$this->_problem->populateWithArray($problemList);
		}
		//trigger_error($this->_problem->toString(),E_USER_NOTICE);
		$this->_problem->lastUpdated = date('Y-m-d H:i:s');
		$this->_problem->persist();
		$data = array();
		$data['msg'] = __('Changes applied.');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxAnnotateAction() {
		$problemListId = $this->_getParam('problemListId');
		$comment = $this->_getParam('comment');
		if (strlen($problemListId) <= 0) {
			throw new Exception(__('Empty Problem List ID'));
		}

		$problemListComment = new ProblemListComment();
		$problemListComment->problemListId = $problemListId;
		$problemListComment->setAuthorId((int)Zend_Auth::getInstance()->getIdentity()->personId);
		// we need to populate person
		// if not populated, this may alter the person entry (don't know why?)
		$problemListComment->author->populate();
		$problemListComment->date = date('Y-m-d');
		$problemListComment->comment = $comment;
		$problemListComment->persist();

		$rows = array('msg'=>__('Successfully saved.'));
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxChangeStatusAction() {
		$problemListId = (int)$this->_getParam('problemListId');
		$newStatus = $this->_getParam('newStatus');
		$restore = $this->_getParam('restore');
		if (strlen($problemListId) <= 0) {
			throw new Exception(__('Empty Problem List ID'));
		}

		if (strlen($newStatus) > 0 && !in_array($newStatus,$this->_status)) {
			throw new Exception(__('Invalid Status'));
		}

		$problemList = new ProblemList();
		$problemList->problemListId = $problemListId;
		$problemList->populate();
		// check if restore
		if (strlen($restore) > 0) { // request to restore
			// restore the previous status and consider it as $newStatus
			$newStatus = $problemList->previousStatus;
		}

		// copy the current status before overriding
		$problemList->previousStatus = $problemList->status;
		$problemList->status = $newStatus;
		$problemList->persist();

		$rows = array('msg'=>__('Successfully changed.'));
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function viewProblemAction() {
		$problemListId = (int)$this->_getParam('problemListId');
		if (strlen($problemListId) <= 0) {
			throw new Exception(__('Empty Problem List ID'));
		}
		$problemList = new ProblemList();
		$problemList->problemListId = (int)$problemListId;
		$problemList->populate();
		$this->view->problemList = $problemList;
		$this->render('view-problem');
	}

	/* STUB METHODS */
	protected function getServices() {
		$services = array();
		//$services[] ='';
		return $services;
	}

	public function processSetFlagsAction() {
		$problemListId = (int)$this->_getParam('problemListId');
		$flags = $this->_getParam('flags');

		$data = false;
		if ($problemListId > 0 && strlen($flags) > 0) {
			$problemList = new ProblemList();
			$problemList->problemListId = $problemListId;
			if ($problemList->populate()) {
				$problemList->flags = $flags;
				$problemList->persist();
				$data = true;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
