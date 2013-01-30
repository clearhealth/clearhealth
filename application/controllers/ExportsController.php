<?php
/*****************************************************************************
*       ExportsController.php
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


class ExportsController extends WebVista_Controller_Action {

	public function hl7LabtestResultsAction() {
		$personId = (int)$this->_getParam('personId');
		$providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$contents = LabResultsMessage::generate($personId,$providerId);
		$filename = 'hl7-labtest-results-'.$personId.'.er7';
		$this->hl7($contents,$filename);
	}

	public function hl7ImmunizationsAction() {
		$personId = (int)$this->_getParam('personId');
		$contents = HL7Immunization::generate($personId);
		$filename = 'hl7-immunizations-'.$personId.'.er7';
		$this->hl7($contents,$filename);
	}

	protected function hl7($contents,$filename) {
		$this->getResponse()->setHeader('Content-Type','application/binary');
		$this->getResponse()->setHeader('Content-Disposition','attachment; filename="'.$filename.'"');
		$this->view->contents = $contents;
		$this->render('hl7');
	}

}
