<?php
/*****************************************************************************
*       CcrController.php
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


class CcrController extends WebVista_Controller_Action {

	public function viewAction() {
		$personId = (int)$this->_getParam('personId');
		$visitId = (int)$this->_getParam('visitId');
		$providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$auditType = ($visitId > 0)?GenericAccessAudit::CCD_VISIT_VIEW:GenericAccessAudit::CCD_ALL_VIEW;
		$this->_createAudit($providerId,$personId,$visitId,$auditType);
		$this->view->personId = $personId;
		$this->view->visitId = $visitId;
		$this->render();
	} 

	public function viewXmlAction() {
		$personId = (int)$this->_getParam('personId');
		$visitId = (int)$this->_getParam('visitId');
		$providerId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$ccd = new CCD(true);
		$contents = $ccd->populate($personId,$providerId,$visitId);
		header('Content-Type: text/xml;');
		$this->view->contents = $contents;
		$this->render();
	} 

	public function xslAction() {
		header('Content-Type: text/xml;');
		$this->render();
	}

	public function viewUploadedHl7Action() {
		$filename = $this->_getParam('file','');
		$type = strtolower($this->_getParam('type',''));
		$type = ($type != 'ccd' && $type != 'ccr')?'ccd':$type;
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
			$xml = new SimpleXMLElement($contents);
			$contents = $xml->asXML();
			$x = explode("\n",$contents);
			$xsl = '<?xml-stylesheet type="text/xsl" href="'.Zend_Registry::get('baseUrl').'/'.$type.'.raw/xsl"?>';
			$contents = preg_replace('/<\?(.*)\?>/','<?$1?>'.$xsl,$contents);
			header('Content-Type: text/xml;');
		}
		$this->view->contents = $contents;
		$this->render('view-xml');
	}

	public function processHl7ViewAction() {
		if (isset($_FILES['uploadFile'])) {
			$errorCode = $_FILES['uploadFile']['error'];
			if ($errorCode == UPLOAD_ERR_OK) {
				$fileData = file_get_contents($_FILES['uploadFile']['tmp_name']);
				try {
					$xml = new SimpleXMLElement($fileData);
					$contents = $xml->asXML();
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
					$data = array('filename'=>$fn,'type'=>$this->_getParam('type'));
				}
				catch (Exception $e) {
					$data = array('error'=>'XML parse error: '.$e->getMessage());
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
		$this->render();
	} 

	public function viewUploadProgressAction() {
		$status = apc_fetch('upload_'.$this->_getParam('uploadKey'));
		$percent = ($status['current'] > 0)?($status['current']/$status['total']*100):0;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($percent);
	}

}
