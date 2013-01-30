<?php
/*****************************************************************************
*       ElcpController.php
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


class ElcpController extends WebVista_Controller_Action  {

    public function pidLookupAction() {
		$hl7 = $this->_getParam('DATA');
		
		$fields = split("\|",$hl7);
		$personId = 0;
		if (count($fields) > 18) {
			$personId = (int)$fields[19]; //field containing the MRN
		}

		$patient = new Patient();
		$patient->personId = $personId;
		if ($personId > 0 && $patient->populate()) {
			urlencode($this->render('pid-lookup.phtml'));
		}
		return urlencode($this->render('not-found'));

    }

}
