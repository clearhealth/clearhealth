<?php
/*****************************************************************************
*       Hl7MessagesController.php
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


class Hl7MessagesController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function postHl7MessageAction() {
		$message = $this->_getParam('message');
		$hl7Message = new HL7Message();
		$hl7Message->message = $message;
		$hl7Message->type = 'HL7';
		$hl7Message->persist();

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}
}
