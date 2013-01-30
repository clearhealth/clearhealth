<?php
/*****************************************************************************
*       AdminPharmaciesController.php
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


class AdminPharmaciesController extends WebVista_Controller_Action {
	protected $_form;
	protected $_pharmacy;

	public function preDispatch() {
		parent::preDispatch();
		$this->view->addPharmacy = false;
	}

	public function indexAction() {
		$this->render();
	}

	public function addAction() {
		$this->getHelper('viewRenderer')->setNoRender();
		$this->editAction();
		$this->getResponse()->clearBody();
		$this->view->addPharmacy = true;
		$this->render('edit');
	}
	
	public function processEditAction() {
		$params = $this->_getParam('pharmacy');
		if (isset($params['preferred'])) {
			$params['preferred'] = 1;
		}
		$pharmacy = new Pharmacy();
		$pharmacy->populateWithArray($params);
		//$pharmacy->pharmacyId = Pharmacy::generateGUID();
		$pharmacy->LastModifierDate = date('Y-m-d H:i:s');
		$pharmacy->persist();
		$data['pharmacyId'] = $pharmacy->pharmacyId;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editAction() {
		$pharmacyId = preg_replace('/[^a-zA-Z0-9-]+/','',$this->_getParam('pharmacyId'));
		if (isset($this->_session->messages)) {
			$this->view->messages = $this->_session->messages;
		}
		$this->_form = new WebVista_Form(array('name' => 'pharmacy-detail'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "admin-pharmacy.raw/process-edit");
		$this->_pharmacy = new Pharmacy();
		$this->_pharmacy->pharmacyId = $pharmacyId;
		if (!$this->_pharmacy->populate()) {
			$this->_pharmacy->RecordChange = 'N';
		}
		$this->_form->loadORM($this->_pharmacy, "Pharmacy");
		//var_dump($this->_form);
		$this->view->form = $this->_form;
		$this->view->pharmacy = $this->_pharmacy;
		$this->render('edit');
	}

	public function autoCompleteAction() {
		$match = $this->_getParam('name');
		$match = preg_replace('/[^a-zA-Z-0-9\ ]/','',$match);
		$matches = array();
		if (!strlen($match) > 0) {
			$this->_helper->autoCompleteDojo($matches);
		}
		$db = Zend_Registry::get('dbAdapter');
		$patSelect = $db->select()
				->from('pharmacies')
				->where('pharmacies.StoreName like ' . $db->quote($match.'%'))
				->order('pharmacies.State DESC')
				->order('pharmacies.City DESC')
				->order('pharmacies.StoreName DESC')
				->limit(50);
		//echo $patSelect->__toString();exit;
		//var_dump($db->query($patSelect)->fetchAll());exit;
		foreach($db->query($patSelect)->fetchAll() as $row) {
			$matches[$row['pharmacyId']] = $row['StoreName'] . ' ' . $row['City'] . ' ' .  $row['State'];
		}
		//var_dump($matches);exit;
		//$matches = array("name1" => $match, "name2" =>"value3");
		$this->_helper->autoCompleteDojo($matches);
	}

	public function healthcloudSyncAction() {
		$this->render();
	}

	public function ajaxActivateDownloadUrlAction() {
		$data = Pharmacy::activateDownload((int)$this->_getParam('daily'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxDownloadPharmaciesFileAction() {
		$filename = urldecode($this->_getParam('filename'));
		$cookieFile = urldecode($this->_getParam('cookieFile'));
		$tmpFileName = Pharmacy::downloadPharmacy($filename,$cookieFile);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('filename'=>$tmpFileName));
	}

	public function ajaxLoadPharmaciesDataAction() {
		$counter = Pharmacy::loadPharmacy(urldecode($this->_getParam('filename')));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($counter);
	}
}
