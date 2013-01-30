<?php
/*****************************************************************************
*       EmergencyAccessController.php
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


class EmergencyAccessController extends WebVista_Controller_Action {

	public function indexAction() {
		$data = true;
		$enable = (int)$this->_getParam('enable');
		if (file_exists('/tmp/emergency')) {
			$data = array('error'=>'Emergency Access is now disabled.');
			unlink ('/tmp/emergency');
		}
		else if ($enable === 1) {
			file_put_contents('/tmp/emergency',1);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function allowEmergencyAccessAction() {
		return false;
	}

}
