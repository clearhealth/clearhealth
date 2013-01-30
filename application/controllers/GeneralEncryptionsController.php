<?php
/*****************************************************************************
*       GeneralEncryptionsController.php
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


class GeneralEncryptionsController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	} 

	public function processEncryptAction() {
		$this->_processCrypt();
	}

	public function processDecryptAction() {
		$this->_processCrypt(false);
	}

	protected function _processCrypt($encrypt = true) {
		if (isset($_FILES['uploadFile'])) {
			$errorCode = $_FILES['uploadFile']['error'];
			if ($errorCode == UPLOAD_ERR_OK) {
				$fileData = file_get_contents($_FILES['uploadFile']['tmp_name']);
				$passphrase = $this->_getParam('passphrase','');
				if ($encrypt) {
					trigger_error('encrypting uploaded file.');
					$contents = GeneralEncryption::encryptAES256($fileData,$passphrase);
				}
				else {
					trigger_error('decrypting uploaded file.');
					$contents = GeneralEncryption::decryptAES256($fileData,$passphrase);
				}
				$ds = DIRECTORY_SEPARATOR;
				$personId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
				$basePath = Zend_Registry::get('basePath');
				$filePath = "{$basePath}{$ds}data{$ds}tmp{$ds}{$personId}";
				if (!file_exists($filePath)) {
					trigger_error("Path '$filePath' does not exists, creating...");
					if (mkdir($filePath,0777,true) === false) trigger_error('Failed to create directory: '.$filePath);
				}
				$fn = uniqid('');
				$filename = $filePath.$ds.$fn;
				if (file_put_contents($filename,$contents) === false) trigger_error('Failed to create file: '.$filename);
				$data = array('filename'=>$fn);
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
				$data = array('error'=>$error);
			}
		}
		else {
			$data = array('error'=>'No uploaded file.');
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
       	        $json->suppressExit = true;
		$jsonData = $json->direct($data,false);
		$this->getResponse()->setHeader('Content-Type', 'text/html');
		$this->view->result = $jsonData;
		$this->render('process-crypt');
	}

	public function viewUploadProgressAction() {
		$status = apc_fetch('upload_'.$this->_getParam('uploadKey'));
		$percent = ($status['current'] > 0)?($status['current']/$status['total']*100):0;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($percent);
	}

	public function downloadAction() {
		$filename = $this->_getParam('filename','');
		$ds = DIRECTORY_SEPARATOR;
		$personId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$basePath = Zend_Registry::get('basePath');
		$filePath = "{$basePath}{$ds}data{$ds}tmp{$ds}{$personId}{$ds}{$filename}";
		if (!file_exists($filePath)) {
			$contents = "File '$filename' does not exists.";
			trigger_error($contents);
		}
		else {
			$contents = file_get_contents($filePath);
		}
		$this->view->contents = $contents;
		$this->getResponse()->setHeader('Content-Type','application/binary');
		$this->getResponse()->setHeader('Content-Disposition','attachment; filename="'.$filename.'"');
		$this->render();
	}

	public function processHashAction() {
		if (isset($_FILES['uploadHashFile'])) {
			$errorCode = $_FILES['uploadHashFile']['error'];
			if ($errorCode == UPLOAD_ERR_OK) {
				$fileData = file_get_contents($_FILES['uploadHashFile']['tmp_name']);
				$hash = sha1($fileData);
				$data = array('hash'=>$hash);
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
				$data = array('error'=>$error);
			}
		}
		else {
			$data = array('error'=>'No uploaded file.');
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
       	        $json->suppressExit = true;
		$jsonData = $json->direct($data,false);
		$this->getResponse()->setHeader('Content-Type', 'text/html');
		$this->view->result = $jsonData;
		$this->render('process-crypt');
	}

}
