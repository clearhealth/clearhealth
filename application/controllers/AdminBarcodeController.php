<?php
/*****************************************************************************
*       AdminBarcodeController.php
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


class AdminBarcodeController extends WebVista_Controller_Action {

	protected $_form = null;
	protected $_barcodeMacro = null;

	/**
	 * Default action
	 */
	public function indexAction() {
	}

	/**
	 * Toolbar xml structure
	 */
	public function toolbarAction() {
		// utilize the common toolbar method defined at WebVista_Controller_Action
		$this->_renderToolbar("toolbar");
	}

	/**
	 * Returns JSON format for lists all barcode macros
	 */
	public function listMacrosAction() {
		$barcodeMacroIterator = new BarcodeMacroIterator();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$barcodeMacroIterator->toJsonArray('name',array('name'))));
	}

	/**
	 * Render edit page
	 */
	public function editAction() {
		$name = $this->_getParam("name",null);
		$this->_barcodeMacro = new BarcodeMacro();
		if (strlen($name) > 0) {
			$this->_barcodeMacro->name = $name;
			$this->_barcodeMacro->populate();
		}
		$this->_form = new WebVista_Form(array("name"=>"edit-barcode"));
                $this->_form->setAction(Zend_Registry::get('baseUrl') . "admin-barcode.raw/process-edit");
		$this->_form->loadORM($this->_barcodeMacro,"barcodeMacro");
		$this->_form->setWindow("windowEditBarcodeMacroId");
		$this->view->form = $this->_form;
		$this->render("edit");
	}

	/**
	 * Process modifications
	 */
	public function processEditAction() {
		$this->editAction();
		$params = $this->_getParam("barcodeMacro");
		$this->_barcodeMacro->populateWithArray($params);
		$this->_barcodeMacro->persist();
		$this->view->message = __("Record saved successfully");
		$this->render("edit");
	}

	/**
	 * Process delete
	 */
	public function processDeleteAction() {
		$name = preg_replace('/[^0-9a-z_A-Z- \.]/','',$this->_getParam("name"));
		$barcodeMacro = new BarcodeMacro();
		$barcodeMacro->name = $name;
		$barcodeMacro->setPersistMode(WebVista_Model_ORM::DELETE);
		$barcodeMacro->persist();
		$data = array();
		$data['msg'] = __("Record deleted successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	/**
	 * Process reorder
	 */
	public function processReorderAction() {
		$nameFrom = $this->_getParam("nameFrom");
		$nameTo = $this->_getParam("nameTo");
		$barcodeMacroFrom = new BarcodeMacro();
		$barcodeMacroFrom->reorder($nameFrom,$nameTo);
		$data = array();
		$data['msg'] = __("Record deleted successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
