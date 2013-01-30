<?php
/*****************************************************************************
*       BarcodeController.php
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


class BarcodeController extends WebVista_Controller_Action {

	public function handleReadAction()  {
		$barcodeString = $this->_getParam("barcodeString");
		$barcodeMacroIterator = new BarcodeMacroIterator();
		$data = array();
		$data['ret'] = false;
		foreach ($barcodeMacroIterator as $barcode) {
			if (preg_match($barcode->regex,$barcodeString)) {
				$macro = array();
				$macro['name'] = preg_replace('/[^a-z_]/i','',$barcode->name);
				$macro['macro'] = $barcode->macro;
				$macro['regex'] = $barcode->regex;
				$macro['order'] = $barcode->order;
				$data['ret'] = $macro;
				break;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
