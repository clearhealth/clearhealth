<?php
/*****************************************************************************
*       UpdateManagerController.php
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


/**
 * Update Manager controller
 */
class UpdateManagerController extends WebVista_Controller_Action {

	protected $_session = null;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	/**
	 * Default action to dispatch
	 */
	public function indexAction() {
		//$this->_download(); // download update files automatically
		$this->render('index');
	}

	public function toolbarAction() {
		$this->view->xmlHeader = '<?xml version="1.0" encoding="iso-8859-1"?>'.PHP_EOL;
		header('Content-Type: text/xml');
		$this->render('toolbar');
	}

	public function listXmlAction() {
		$baseStr = "<?xml version='1.0' standalone='yes'?><rows></rows>";
		$xml = new SimpleXMLElement($baseStr);
		$updateFile = new UpdateFile();
		$updateFileIterator = $updateFile->getIteratorActive();
		$alterTable = new AlterTable();
		$channel = null;
		$ctr = 1;
		foreach ($updateFileIterator as $item) {
			if ($channel === null || $channel != $item->channel) {
				$channel = $item->channel;
				$channelXml = $xml->addChild('row',$channel);
				$channelXml->addAttribute('id',$ctr++);
				$channelXml->addChild('cell',$channel);
			}
			$parent = $channelXml->addChild('row');
			$parent->addAttribute('id',$item->updateFileId);
			$parent->addChild('cell',$item->name.' (v'.$item->version.')');
			$parent->addChild('cell',$item->status);
			$parent->addChild('cell','');
		}
		header('content-type: text/xml');
		$this->view->content = $xml->asXml();
		$this->render('list-xml');
	}

	public function applyAction() {
		$updateFileId = (int)$this->_getParam('updateFileId');
		$updateFile = new UpdateFile();
		$updateFile->updateFileId = $updateFileId;
		$updateFile->populate();
		$license = $updateFile->license;
		if (!strlen($license) > 0) {
			$license = <<<EOL
       Author:  ClearHealth Inc. (www.clear-health.com)        2009
       
       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
       respective logos, icons, and terms are registered trademarks 
       of ClearHealth Inc.

       Though this software is open source you MAY NOT use our 
       trademarks, graphics, logos and icons without explicit permission. 
       Derivitive works MUST NOT be primarily identified using our 
       trademarks, though statements such as "Based on ClearHealth(TM) 
       Technology" or "incoporating ClearHealth(TM) source code" 
       are permissible.

       This file is licensed under the GPL V3, you can find
       a copy of that license by visiting:
       http://www.fsf.org/licensing/licenses/gpl.html
EOL;
		}
		$updateFile->license = $license;
		$this->view->updateFile = $updateFile;
		$this->render('apply');
	}

	public function processApplyAction() {
		$updateFileId = (int)$this->_getParam('updateFileId');
		$updateFile = new UpdateFile();
		$updateFile->updateFileId = $updateFileId;
		$updateFile->populate();

		$ok = true;
		$ret = __('Failed to apply.');
		$notes = unserialize($updateFile->notes);
		if (isset($notes['validateApi'])) {
			try {
				$validateApi = new SimpleXMLElement($notes['validateApi']);
				if ($validateApi->object) {
					$className = (string)$validateApi->object;
					$methodName = (string)$validateApi->method;
					$params = array($updateFileId);
					foreach ($validateApi->argument as $argument) {
						$params[] = (string)$argument;
					}
					if (!class_exists($className) || !method_exists($className,$methodName)) {
						throw new Exception('Your install may be out of date or is out of date for this update.'.$className.' '.$methodName);
					}
					if (strtolower(substr($className,-10)) == 'controller' && strtolower(substr($methodName,-6)) == 'action') {
						$controller = substr(strtolower(preg_replace('/([A-Z]{1})/','-\1',substr($className,0,(strlen($className)-10)))),1);
						$action = strtolower(preg_replace('/([A-Z]{1})/','-\1',substr($methodName,0,strlen($methodName)-6)));
						$ctr = count($params);
						$arg = array('updateFileId'=>$updateFileId);
						for ($i=1,$ctr=count($params);$i<$ctr;$i++) {
							$arg[$i] = $params[$i];
						}
						$ret = array('url'=>Zend_Registry::get('baseUrl').$controller.'.raw/'.$action.'?'.http_build_query($arg));
					}
					else {
						$ret = array('data'=>call_user_func_array(array($className,$methodName),$params));
					}
					$ok = false;
				}
				else if ($validateApi->function) {
					$functionName = (string)$validateApi->function;
					$params = array($updateFileId);
					foreach ($validateApi->argument as $argument) {
						$params[] = (string)$argument;
					}
					if (!function_exists($functionName)) {
						throw new Exception('Your install may be out of date or is out of date for this update.');
					}
					$ret = array('data'=>call_user_func_array($functionName,$params));
					$ok = false;
				}
			}
			catch (Exception $e) {
				$ret = array('error'=>$e->getMessage());
				$ok = false;
			}
		}
		if ($ok) {
			$updateFile->install();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function checkAction() {
		$data = array();
		$sessVersions = array();
		$updateFile = new UpdateFile();
		$output = $this->_fetch('check',array('versions'=>$updateFile->getAllVersions()));
		if ($output === false) {
			$data['code'] = 400;
			$data['msg'] = __('There was an error connecting to HealthCloud');
			trigger_error($output,E_USER_NOTICE);
		}
		else {
			$xml = simplexml_load_string($output);
			if (isset($xml->error)) {
				$data['code'] = 201;
				$data['msg'] = (string)$xml->error->errorMsg;
			}
			else {
				$data['code'] = 200;
				$ctr = 0;
				foreach ($xml as $update) {
					$tmp = array();
					foreach ($update as $key=>$value) {
						$tmp[$key] = (string)$value;
					}
					$sessVersions[$tmp['id']] = $tmp;
					$ctr++;
				}
				$data['msg'] = $sessVersions;
				$data['len'] = $ctr;
			}
		}
		$this->_session->versions = $sessVersions;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function downloadAction() {
		$updateId = (int)$this->_getParam('id');
		$version = $this->_session->versions[$updateId];

		$updateFile = new UpdateFile();
		$uploadDir = $updateFile->getUploadDir();
		$error = false;
		if (!is_dir($uploadDir)) {
			$error = $uploadDir.' directory does not exists';
			trigger_error($error,E_USER_NOTICE);
		}
		else if (!is_writable($uploadDir)) {
			$error = $uploadDir.' directory is not writable';
			trigger_error($error,E_USER_NOTICE);
		}

		if ($error !== false) {
			$ret = $error;
		}
		else if (($ret = $this->_fetch('download',array('updateFileId'=>$updateId))) === false) {
			$ret = __('There was an error connecting to HealthCloud');
			trigger_error($ret,E_USER_NOTICE);
		}
		else {
			try {
				$filename = tempnam(sys_get_temp_dir(),'uf_');
				file_put_contents($filename,$ret);

				$updateFile->active = 1;
				$updateFile->dateTime = date('Y-m-d H:i:s');
				try {
					$updateFile->populateWithArray($this->_session->versions[$updateId]);
					if (substr($updateFile->name,-3) == '.gz') {
						$updateFile->name = substr($updateFile->name,0,-3);
					}
					$updateFile->version = $version['version'];
					$updateFile->persist();
					$contents = $updateFile->verify($filename);
					unset($this->_session->versions[$updateId]);
					$ret = true;
					list($index,$next) = each($this->_session->versions);
					if ($next !== null) {
						$ret = array('next'=>$next);
					}
				}
				catch (Exception $e) {
					$error = __('Invalid signature');
					$msg = $error.': '.$e->getMessage();
					try {
						$xml = new SimpleXMLElement($ret);
						if ($xml->error) {
							$msg = (string)$xml->error->errorMsg;
						}
					}
					catch (Exception $ex) {
					}
					$ret = $msg;
					$updateFile->setPersistMode(WebVista_Model_ORM::DELETE);
					trigger_error($ret,E_USER_NOTICE);
				}
				$updateFile->persist();
			}
			catch (Exception $e) {
				$error = __('There was an error with the downloaded file');
				$ret = $error.': '.$e->getMessage();
				$error .= ' Error code: '.$e->getCode().' Error Message: '.$e->getMessage();
				trigger_error($error,E_USER_NOTICE);
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	protected function _fetch($action,Array $versions) {
		$ch = curl_init();
		$updateServerUrl = Zend_Registry::get('config')->healthcloud->updateServerUrl;
		$updateServerUrl .= '/'.$action;
		$xml = new SimpleXMLElement('<clearhealth/>');
		$xml->addChild('apiKey',Zend_Registry::get('config')->healthcloud->apiKey);
		$xml->addChild('userId',(int)Zend_Auth::getInstance()->getIdentity()->personId);
		$xml->addChild('username',Zend_Auth::getInstance()->getIdentity()->username);
		foreach ($versions as $key=>$values) {
			if (is_array($values)) {
				foreach ($values as $id=>$value) {
					$xmlChild = $xml->addChild($key);
					foreach ($value as $k=>$v) {
						$xmlChild->addChild($k,$v);
					}
				}
			}
			else {
				$xml->addChild($key,$values);
			}
		}
		curl_setopt($ch,CURLOPT_URL,$updateServerUrl);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml->asXML());
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		$ret = curl_exec($ch);
		if (curl_errno($ch)) {
			trigger_error(curl_error($ch));
			$ret = false;
		}
		curl_close($ch);
		return $ret;
	}


	public function uploadAction() {
		$updateFile = new UpdateFile();
		$form = new WebVista_Form(array('name'=>'edit'));
		$form->setAction(Zend_Registry::get('baseUrl') . 'update-manager.raw/process-upload');
		$form->loadORM($updateFile,'updateFile');
		$form->setWindow('winNewUploadId');
		$form->setAttrib('enctype','multipart/form-data');
		$this->view->form = $form;
		$this->render('upload');
	}

	public function processUploadAction() {
		$updateFile = new UpdateFile();
		$uploadDir = $updateFile->getUploadDir();

		if (!is_dir($uploadDir)) {
			$msg = $uploadDir.' directory does not exist';
		}
		else if (!is_writable($uploadDir)) {
			$msg = $uploadDir.' directory is not writable';
		}
		else if (!isset($_FILES['uploadFile'])) {
			$msg = __('No uploaded file');
		}
		else if ($_FILES['uploadFile']['error'] !== 0) {
			$msg = __('Error in uploading');
		}
		else if (stripos($_FILES['uploadFile']['type'],'xml') === false) {
			$msg = __('Invalid file format, must be an XML file.');
		}
		else {
			$file = $_FILES['uploadFile'];
		}
		if (isset($msg)) {
			$this->_session->errMsg = $msg;
			throw new Exception($msg);
		}
		$params = $this->_getParam('updateFile');
		$updateFile->channelId = UpdateFile::USER_CHANNEL_ID;
		$updateFile->channel = UpdateFile::USER_CHANNEL;
		$updateFile->active = 1;
		$updateFile->name = $file['name'];
		$updateFile->mimeType = $file['type'];
		$updateFile->md5sum = md5_file($file['tmp_name']);
		$updateFile->description = $params['description'];
		$updateFile->dateTime = date('Y-m-d H:i:s');
		$updateFile->persist();

		move_uploaded_file($file['tmp_name'],$updateFile->getUploadFilename());

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$result = $json->direct(array('updateFileId'=>$updateFile->updateFileId),false);
		$this->getResponse()->setHeader('Content-Type', 'text/html');
		$this->view->result = $result;
		$this->render('process-upload');
	}

	public function viewUploadProgressAction() {
		if (isset($this->_session->errMsg)) {
			$percent = array('err_msg'=>$this->_session->errMsg);
			unset($this->_session->errMsg);
		}
		else {
			$status = apc_fetch('upload_'.$this->_getParam('uploadKey'));
			$percent = 0;
			if ($status['current'] > 0 ) {
				$percent = $status['current']/$status['total']*100;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($percent);
	}

	public function viewDetailsAction() {
		$updateFileId = (int)$this->_getParam('updateFileId');
		$updateFile = new UpdateFile();
		$updateFile->updateFileId = $updateFileId;
		$updateFile->populate();
		$alterTable = new AlterTable();
		$this->view->name = $updateFile->channel.': '.$updateFile->name;
		$this->view->data = $alterTable->generateChanges($updateFile->getUploadFilename());
		$this->render('view-details');
	}

	public function processDeleteAction() {
		$param = $this->_getParam('id');
		$ids = explode(',',$param);
		$ret = false;
		foreach ($ids as $updateFileId) {
			if (!$updateFileId > 0) continue;
			$ret = true;
			$updateFile = new UpdateFile();
			$updateFile->updateFileId = (int)$updateFileId;
			$updateFile->populate();
			if (!strlen($updateFile->version) > 0) continue;
			//$updateFile->active = 0;
			$updateFile->setPersistMode(WebVista_Model_ORM::DELETE);
			$updateFile->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

}

