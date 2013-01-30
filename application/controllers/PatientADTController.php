<?php
/*****************************************************************************
*       PatientADTController.php
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


class PatientADTController extends WebVista_Controller_Action {

	protected $_session;
	protected $_patient;
	protected $_visit;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
		$cprss = new Zend_Session_Namespace('CprsController');
		$this->_patient = $cprss->patient;
		$this->_visit = $cprss->visit;
                $this->_location = $cprss->location;
	}

	public function indexAction() {
		$auth = Zend_Auth::getInstance();
        	var_dump($auth->getIdentity());
		echo $this->_patient->personId;
		echo $this->_visit->admissionId;
		exit;
	}

	function addAdmissionAction() {
		$personId = (int)$this->_getParam('personId', 0);
                $admit = new Admission();
		if (!$personId > 0) {
			$personId = $this->_patient->personId;
		}
                $admit->personId = (int)$personId;
                $admit->dateTime = date('Y-m-d H:i:s');
                $admit->admittingUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
                $admit->locationId = (int)$this->_location->locationId;
                $admit->persist();
		$this->view->action('set-active-patient','cprs',null,array('personId' => $personId));
		$this->view->action('set-active-visit','cprs',null,array('visitId' => $admit->admissionId));
                $this->_session->_visit = $admit;
		$acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $acj->suppressExit = true;
                $acj->direct(array(true));
	}
}
