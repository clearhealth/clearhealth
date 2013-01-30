<?php
/*****************************************************************************
*       eFaxOutbound.php
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


class eFaxOutbound extends eFax {

	protected $transmissionId = '';
	protected $dispositionUrl = '';
	protected $dispositionLevel = 'BOTH';
	protected $dispositionMethod = 'POST';
	protected $dispositionEmails = array();
	protected $files = array();
	protected $recipients = array();
	protected $faxHeader = '"@DATE1 @TIME3 @ROUTETO{26} @RCVRFAX Pg%P/@TPAGES"';
	protected $priority = 'NORMAL';
	protected $resolution = 'STANDARD';
	protected $selfBusy = 'ENABLE';
	protected $noDuplicate = 'DISABLE';

	protected $docid = '';

	protected $_fileTypes = array('doc','docx','xls','xlsx',
				'ppt','pptx','html','htm',
				'tif','tiff','jpg','jpeg',
				'txt','pdf','rtf','snp',
				'png','gif');


	const DISPOSITION_LEVEL_ERROR = 'ERROR';
	const DISPOSITION_LEVEL_SUCCESS = 'SUCCESS';
	const DISPOSITION_LEVEL_BOTH = 'BOTH';
	const DISPOSITION_LEVEL_NONE = 'NONE';

	const DISPOSITION_METHOD_POST = 'POST';
	const DISPOSITION_METHOD_EMAIL = 'EMAIL';

	const SELF_BUSY_ENABLE = 'ENABLE';
	const SELF_BUSY_DISABLE = 'DISABLE';

	const PRIORITY_NORMAL = 'NORMAL';
	const PRIORITY_HIGH = 'HIGH';

	const RESOLUTION_STANDARD = 'STANDARD';
	const RESOLUTION_FINE = 'FINE';

	const NO_DUPLICATE_ENABLE = 'ENABLE';
	const NO_DUPLICATE_DISABLE = 'DISABLE';

	public function __construct($username = null, $password = null, $accountIdentifier = null, $url = null) {
		parent::__construct($username, $password, $accountIdentifier, $url);
	}

	public function setTransmissionId($transmissionId) {
		$this->transmissionId = $transmissionId;
	}

	public function getTransmissionId() {
		return $this->transmissionId;
	}

	public function setDispositionUrl($dispositionUrl) {
		// required if method is POST
		$this->dispositionUrl = $dispositionUrl;
	}

	public function getDispositionUrl() {
		return $this->dispositionUrl;
	}

	public function setDispositionLevel($dispositionLevel) {
		$this->dispositionLevel = $dispositionLevel;
	}

	public function getDispositionLevel() {
		return $this->dispositionLevel;
	}

	public function setDispositionMethod($dispositionMethod) {
		// POST/EMAIL
		$this->dispositionMethod = $dispositionMethod;
	}

	public function getDispositionMethod() {
		return $this->dispositionMethod;
	}

	public function addDispositionEmail($recipient, $address) {
		// required if method is EMAIL
		$this->dispositionEmails[] = array('recipient'=>$recipient,'address'=>$address);
	}

	public function getDispositionEmails() {
		return $this->dispositionEmails;
	}

	public function addFile($contents, $type) {
		$this->files[] = array('contents'=>$contents,'type'=>$type);
	}

	public function getFiles() {
		return $this->files;
	}

	public function addRecipient($fax, $name='', $company='') {
		$this->recipients[] = array('fax'=>$fax,'name'=>$name,'company'=>$company);
	}

	public function getRecipients() {
		return $this->recipients;
	}

	// OPTIONAL
	public function setFaxHeader($faxHeader) {
		$this->faxHeader = $faxHeader;
	}

	public function getFaxHeader() {
		return $this->faxHeader;
	}

	public function setPriority($priority) {
		$this->priority = $priority;
	}

	public function getPriority() {
		return $this->priority;
	}

	public function setResolution($resolution) {
		$this->resolution = $resolution;
	}

	public function getResolution() {
		return $this->resolution;
	}

	public function setSelfBusy($selfBusy) {
		$this->selfBusy = $selfBusy;
	}

	public function getSelfBusy() {
		return $this->selfBusy;
	}

	public function setNoDuplicate($noDuplicate) {
		$this->noDuplicate = $noDuplicate;
	}

	public function getNoDuplicate() {
		return $this->noDuplicate;
	}

	public function setDocId($docid) {
		$this->docid = $docid;
	}

	public function getDocId() {
		return $this->docid;
	}

	public function send() {
		$this->setErrors(array());
		$ret = false;
		$data = array();
		$data['transmissionId'] = $this->getTransmissionId();
		$data['noDuplicates'] = $this->getNoDuplicate();
		$data['resolution'] = $this->getResolution();
		$data['priority'] = $this->getPriority();
		$data['selfBusy'] = $this->getSelfBusy();
		$data['faxHeader'] = $this->getFaxHeader();
		$data['dispositionMethod'] = self::DISPOSITION_METHOD_POST;
		$dispositionUrl = $this->getDispositionUrl();
		if (!strlen($dispositionUrl) > 0) {
			$dispositionUrl = Zend_Registry::get('config')->healthcloud->eFax->dispositionUrl;
		}
		$data['dispositionUrl'] = $dispositionUrl;
		$data['dispositionLevel'] = $this->getDispositionLevel();
		$recipients = $this->getRecipients();
		if (count($recipients) > 0) {
			$data['recipients'] = $recipients;
		}
		else {
			$this->addError(__('At least one recipient is required'));
			return $ret;
		}
		$files = $this->getFiles();
		if (count($files) > 0) {
			$data['files'] = $files;
		}
		else {
			$this->addError(__('At least one file is required'));
			return $ret;
		}
		//file_put_contents('/tmp/efax.inbound',print_r($data,true),FILE_APPEND);
		$query = http_build_query(array('data'=>$data));
		//file_put_contents('/tmp/efax.inbound',$query,FILE_APPEND);
		$response = $this->transmit($query);
		if ($response) {
			if ($responseXml = simplexml_load_string($response)) {
				$statusCode = null;
				if (isset($responseXml->error)) {
					$errorCode = (string)$responseXml->error->errorCode;
					$errorMsg = (string)$responseXml->error->errorMsg;
					$this->addError($errorCode.': '.$errorMsg);
				}
				else {
					$this->docid = (string)$responseXml->docid;
					$ret = true;
				}
			}
			else {
				// error parsing xml
				$this->addError(__('XML parsing error'));
			}
		}
		else {
			// error connecting
			$this->addError(__('Response error'));
		}
		return $ret;
	}

	protected function _preCurlExec($ch) {
		//curl_setopt($ch,CURLOPT_USERPWD,'admin:ch3!');
	}

}
