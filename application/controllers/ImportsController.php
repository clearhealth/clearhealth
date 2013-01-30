<?php
/*****************************************************************************
*       ImportsController.php
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


class ImportsController extends WebVista_Controller_Action {

	public function hl7LabAction() {
		$this->view->personId = (int)$this->_getParam('personId');
		$this->render();
	}

	public function processHl7LabAction() {
		if (isset($_FILES['uploadFile'])) {
			$errorCode = $_FILES['uploadFile']['error'];
			if ($errorCode == UPLOAD_ERR_OK) {
				$contents = file_get_contents($_FILES['uploadFile']['tmp_name']);
				try {
					$data = Import::hl7Lab($contents,(int)$this->_getParam('personId'));
				}
				catch (Exception $e) {
					$data = $e->getMessage();
				}
			}
			else {
				$error = 'Error unknown.';
				switch ($errorCode) {
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						$error = 'The uploaded file exceeds the allowable filesize.';
						break;
					case UPLOAD_ERR_PARTIAL:
						$error = 'The uploaded file was only partially uploaded';
						break;
					case UPLOAD_ERR_NO_FILE:
						$error = 'No file was uploaded.';
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						$error = 'Missing a temporary folder.';
						break;
					case UPLOAD_ERR_CANT_WRITE:
						$error = 'Failed to write file to disk.';
						break;
				}
				$data = $error;
			}
		}
		else {
			$data = 'No uploaded file.';
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
       	        $json->suppressExit = true;
		$jsonData = $json->direct($data,false);
		$this->getResponse()->setHeader('Content-Type', 'text/html');
		$this->view->result = $jsonData;
		$this->render('process');
	} 

	public function viewUploadProgressAction() {
		$status = apc_fetch('upload_'.$this->_getParam('uploadKey'));
		$percent = ($status['current'] > 0)?($status['current']/$status['total']*100):0;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($percent);
	}

}
