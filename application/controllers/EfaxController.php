<?php
/*****************************************************************************
*       EfaxController.php
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


class EfaxController extends WebVista_Controller_Action {

	public function transmitAction() {
		$efax = new eFaxOutbound();
		$data = array();
		$efax->setTransmissionId('10001');
		//$efax->setDispositionMethod('POST');
		//$efax->setDispositionUrl('https://ec2-67-202-27-183.compute-1.amazonaws.com/efax.raw/inbound');
		$efax->setDispositionMethod('EMAIL');
		$efax->addDispositionEmail('Arthur Layese','arthur@layese.com');
		$efax->addRecipient('6022976632','Jay Walker','ClearHealth Inc.');
		$fileTypes = array('doc','docx','xls','xlsx','ppt','pptx','html','htm','tif','tiff','jpg','jpeg','txt','pdf','rtf','snp','png','gif');
		$basePath = Zend_Registry::get('basePath');
		$filename = $basePath.'Sample.tif';
		$fileType = pathinfo($filename,PATHINFO_EXTENSION);
		if (!in_array($fileType,$fileTypes)) {
			return false;
		}
		$contents = file_get_contents($filename);
		$efax->addFile(base64_encode($contents),$fileType);
		$ret = $efax->transmit();
		print_r($ret);
		die;
	}

}
