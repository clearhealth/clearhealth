<?php
/*****************************************************************************
*       ClaimEditsManagerController.php
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


class ClaimEditsManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function listAction() {
		$rows = array();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function addAction() {
		$this->_edit();
		$this->render('edit');
	}

	public function editAction() {
		$this->_edit($this->_getParam('id'));
		$this->render('edit');
	}

	protected function _edit($id=null) {
	}

	public function processAddAction() {
		$this->_processEdit();
	}

	public function processEditAction() {
		$this->_processEdit($this->_getParam('id'));
	}

	protected function _processEdit($id=null) {
	}

	public function processDeleteAction() {
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

}
