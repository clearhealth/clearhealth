<?php
/*****************************************************************************
*       Pharmacy.php
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


class Pharmacy extends WebVista_Model_ORM {

	protected $pharmacyId;
	protected $NCPDPID;
	protected $StoreNumber;
	protected $ReferenceNumberAlt1;
	protected $ReferenceNumberAlt1Qualifier;
	protected $StoreName;
	protected $AddressLine1;
	protected $AddressLine2;
	protected $City;
	protected $State;
	protected $Zip;
	protected $PhonePrimary;
	protected $Fax;
	protected $Email;
	protected $PhoneAlt1;
	protected $PhoneAlt1Qualifier;
	protected $PhoneAlt2;
	protected $PhoneAlt2Qualifier;
	protected $PhoneAlt3;
	protected $PhoneAlt3Qualifier;
	protected $PhoneAlt4;
	protected $PhoneAlt4Qualifier;
	protected $PhoneAlt5;
	protected $PhoneAlt5Qualifier;
	protected $ActiveStartTime;
	protected $ActiveEndTime;
	protected $ServiceLevel;
	protected $PartnerAccount;
	protected $LastModifierDate;
	protected $TwentyFourHourFlag;
	protected $CrossStreet;
	protected $RecordChange;
	protected $OldServiceLevel;
	protected $TextServiceLevel;
	protected $TextServiceLevelChange;
	protected $Version;
	protected $NPI;
	protected $preferred;
	protected $print;

	protected $_table = "pharmacies";
	protected $_primaryKeys = array("pharmacyId");

	function __construct() {
                parent::__construct();
        }
	function setServiceLevel($val) {
		$this->ServiceLevel = (int)$val;
	}
	function getNewRxSupport() {
		if (($this->ServiceLevel & 1) == 1) {
			return 'Y';
		}
		return 'N';
	}

	function getRefReqSupport() {
		if (($this->ServiceLevel &  2) == 2) {
			return 'Y';
		}
		return 'N';
	}

	function getRxFillSupport() {
		if (($this->ServiceLevel & 4) == 4) {
			return 'Y';
		}
		return 'N';
	}

	function getRxChgSupport() {
		if (($this->ServiceLevel & 8) == 8) {
			return 'Y';
		}
		return 'N';
	}

	function getCanRx() {
		if (($this->ServiceLevel & 16) == 16) {
			return 'Y';
		}
		return 'N';
	}

	function getRxHisSupport() {
		if (($this->ServiceLevel & 32) == 32) {
			return 'Y';
		}
		return 'N';
	}

	function getRxEligSupport() {
		if (($this->ServiceLevel & 64) == 64) {
			return 'Y';
		}
		return 'N';
	}
	function getServiceLineDisplay() {
		$serviceStr = "";
		$serviceStr = "NewRX:" . $this->getNewRxSupport() . " ";
		$serviceStr .= "RefReq:" . $this->getRefReqSupport() . " ";
		$serviceStr .= "RxFill:" . $this->getRxFillSupport() . " ";
		$serviceStr .= "RxChg:" . $this->getRxChgSupport() . " ";
		$serviceStr .= "CanRx:" . $this->getCanRx()  . " ";
		$serviceStr .= "RxHis:" . $this->getRxHisSupport() . " ";
		$serviceStr .= "RxElig:" . $this->getRxEligSupport() ;
		return $serviceStr;
	}

	public function sendPharmacy() {
		$uuid = uuid_create();
		$messageId = str_replace('-','',$uuid);
		$messaging = new Messaging();
		$messaging->messagingId = $messageId;
		$messaging->messageType = 'AddPharmacy';
		$messaging->populate();
		$messaging->objectId = $this->pharmacyId;
		$messaging->objectClass = 'Pharmacy';
		$messaging->status = 'Sending';
		$messaging->note = 'Sending new pharmacy';
		$type = 'add';
		switch ($this->RecordChange) {
			case 'N': // New
				break;
			case 'D':
				break;
			default: // or case 'U': // Update
				$type = 'update';
				$messaging->messageType = 'UpdatePharmacy';
				$messaging->note = 'Sending update pharmacy';
				break;
		}
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		$data = $this->toArray();
		$data['messageId'] = $messageId;

		$query = http_build_query(array('type'=>$type,'data'=>$data));
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/edit-pharmacy?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch);
		$error = '';
		$messaging->status = 'Sent';
		$messaging->note = 'Add Pharmacy sent';
		if ($type == 'update') {
			$messaging->note = 'Update Pharmacy sent';
		}
		if (!curl_errno($ch)) {
			try {
				$responseXml = new SimpleXMLElement($output);
				if (isset($responseXml->error)) {
					$errorCode = (string)$responseXml->error->code;
					$errorMsg = (string)$responseXml->error->message;
					if (isset($responseXml->error->errorCode)) {
						$errorCode = (string)$responseXml->error->errorCode;
					}
					if (isset($responseXml->error->errorMsg)) {
						$errorMsg = (string)$responseXml->error->errorMsg;
					}
					$error = $errorMsg;
					trigger_error('There was an error prescribing new medication, Error code: '.$errorCode.' Error Message: '.$errorMsg,E_USER_NOTICE);
				}
				else if (isset($responseXml->status)) {
					if ((string)$responseXml->status->code == '010') { // value 000 is for free standing error?
						$messaging->status .= ' and Verified';
						$messaging->note .= ' and verified';
					}
				}
				if (isset($responseXml->rawMessage)) {
					$messaging->rawMessage = base64_decode((string)$responseXml->rawMessage);
					$messaging->rawMessageResponse = base64_decode((string)$responseXml->rawMessageResponse);
				}
			}
			catch (Exception $e) {
				$error = __("There was an error connecting to HealthCloud to prescribe new medication. Please try again or contact the system administrator.");
				trigger_error("There was an error prescribing new medication, the response couldn't be parsed as XML: " . $output, E_USER_NOTICE);
			}
		}
		else {
			$error = __("There was an error connecting to HealthCloud to prescribe new medication. Please try again or contact the system administrator.");
			trigger_error("Curl error connecting to healthcare prescribed new medication: " . curl_error($ch),E_USER_NOTICE);
		}

		curl_close ($ch);
		$ret = true;
		if (strlen($error) > 0) {
			$messaging->status = 'Error';
			$messaging->note = $error;
			$ret = $error;
		}
		else {
			$this->RecordChange = 'U';
			$this->persist();
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		return $ret;
	}

	public function populatePharmacyIdWithNCPDPID($NCPDPID = null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($NCPDPID === null) {
			$NCPDPID = $this->NCPDPID;
		}
		$sqlSelect = $db->select()
				->from($this->_table,'pharmacyId')
				->where('NCPDPID = ?',(int)$NCPDPID);
		if ($row = $db->fetchRow($sqlSelect)) {
			$this->pharmacyId = $row['pharmacyId'];
		}
	}

	public static function activateDownload($daily) {
		$data = array();
		$data['daily'] = (int)$daily;
		//$data['clinicName'] = $practice->name;
		$type = 'full';
		if ($data['daily']) {
			$type = 'daily';
		}

		$messaging = new Messaging();
		//$messaging->messagingId = '';
		$messaging->messageType = 'DirectoryDownload';
		$messaging->populate();
		//$messaging->objectId = '';
		//$messaging->objectClass = '';
		$messaging->status = 'Downloading';
		$messaging->note = 'Downloading pharmacy ('.$type.')';
		$messaging->dateStatus = date('Y-m-d H:i:s');
		//$messaging->auditId = '';
		$messaging->persist();
		trigger_error($messaging->note,E_USER_NOTICE);

		$ch = curl_init();
		$pharmacyActivateURL = Zend_Registry::get('config')->healthcloud->URL;
		$pharmacyActivateURL .= 'ss-manager.raw/activate-pharmacy-download?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		$cookieFile = tempnam(sys_get_temp_dir(),'ssddcookies_');
		trigger_error('URL: '.$pharmacyActivateURL,E_USER_NOTICE);
		trigger_error('COOKIEFILE: '.$cookieFile,E_USER_NOTICE);
		trigger_error('DATA: '.print_r($data,true));
		curl_setopt($ch,CURLOPT_URL,$pharmacyActivateURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieFile); 
		curl_setopt($ch,CURLOPT_USERPWD,'admin:ch3!');
		$output = curl_exec ($ch);
		$error = "";
		$downloadURL = "";
		$messaging->status = 'Downloaded';
		$messaging->note = 'Pharmacy downloaded ('.$type.')';
		if (!curl_errno($ch)) {
			try {
				$responseXml = simplexml_load_string($output);
				if (isset($responseXml->error)) {
					$error = (string)$responseXml->error->messageCode.': '.(string)$responseXml->error->message;
					trigger_error("There was an error activating synchronization of pharmacies, Error code: " . $responseXml->error->code . " Error Message: " . $responseXml->error->message,E_USER_NOTICE);
				}
				elseif (isset($responseXml->data->SSDirectoryDownloadUrl)) {
					$downloadURL = $responseXml->data->SSDirectoryDownloadUrl;
					trigger_error('DOWNLOAD URL: '.$downloadURL,E_USER_NOTICE);
				}
				if (isset($responseXml->rawMessage)) {
					$messaging->rawMessage = base64_decode((string)$responseXml->rawMessage);
					$messaging->rawMessageResponse = base64_decode((string)$responseXml->rawMessageResponse);
				}
			}
			catch (Exception $e) {
				$error = __("There was an error connecting to HealthCloud to activate synchronization of pharmacies. Please try again or contact the system administrator.");
				trigger_error("Curl error connecting to healthcloud to activate pharmacy sync: " . curl_error($ch),E_USER_NOTICE);
			}
		}
		else {
			$error = __("There was an error connecting to HealthCloud to activate synchronization of pharmacies. Please try again or contact the system administrator.");
			trigger_error("Curl error connecting to healthcloud to activate pharmacy sync: " . curl_error($ch),E_USER_NOTICE);
		}
		curl_close ($ch);
		if (strlen($error) > 0) {
			$messaging->status = 'Error';
			$messaging->note .= ' ERROR: '.$error;
			$ret = false;
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		trigger_error($messaging->note,E_USER_NOTICE);
		return array('downloadUrl'=>$downloadURL,'cookieFile'=>$cookieFile,'error'=>$error);
	}

	public static function downloadPharmacy($downloadURL,$cookieFile) {
		$pharmUpdateFileName = tempnam(sys_get_temp_dir(),'ssdd_');
		trigger_error('URL: '.$downloadURL,E_USER_NOTICE);
		trigger_error('COOKIEFILE: '.$cookieFile,E_USER_NOTICE);
		$pharmUpdateFile = fopen($pharmUpdateFileName,'w');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieFile);
		curl_setopt($ch,CURLOPT_URL,$downloadURL);
		curl_setopt($ch,CURLOPT_POST,false);
		curl_setopt($ch,CURLOPT_HTTPGET,true);
		curl_setopt($ch,CURLOPT_FILE,$pharmUpdateFile);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_USERPWD,'admin:ch3!');
		$output = curl_exec($ch);
		$error = "";
		fclose($pharmUpdateFile);
		$pharmaciesData = "";
		if (!curl_errno($ch)) {
			try {
				$zip = zip_open($pharmUpdateFileName);
				if ($zip) {
					while ($zipEntry = zip_read($zip)) {
						$name = zip_entry_name($zipEntry);
						zip_entry_open($zip,$zipEntry,'r');
						$pharmaciesData = zip_entry_read($zipEntry,zip_entry_filesize($zipEntry));
						zip_entry_close($zipEntry);
					}
					zip_close($zip);
				}
				else {
					$error = __("There was an unpacking the pharmacy data returned from HealthCloud. Please try again or contact the system administrator.");
					trigger_error("Zip error unpacking pharmacy data: " . $zip,E_USER_NOTICE);

				}
			}
			catch (Exception $e) {
				//todo add exceptions in above try
			}
		}
		curl_close ($ch);
		$tmpFileName = tempnam(sys_get_temp_dir(),'ssdddata_');
		$pharmDataTmp = fopen($tmpFileName,'w');
		fwrite($pharmDataTmp,$pharmaciesData);
		fclose($pharmDataTmp);
		return $tmpFileName;
	}

	public static function loadPharmacy($filename) {
		trigger_error('before loading pharmacies: '.calcTS(),E_USER_NOTICE);
		set_time_limit(300); // 5 minutes
		$filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.preg_replace('/.*(\/|\\ee)/','',$filename);
		$pharmDataTmp = fopen($filename,'r');

		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('pharmacies',array('pharmacyId','NCPDPID','preferred'));
		$pharmacies = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$pharmacies[$row['NCPDPID']] = array('pharmacyId'=>$row['pharmacyId'],'preferred'=>$row['preferred']);
			}
		}

		fseek($pharmDataTmp,0);
		$counter = 0;
		while($line = fgets($pharmDataTmp)) {
			$pharmacy = array();
			$pharmacy['NCPDPID'] = substr($line,0,7); 
			$pharmacy['StoreNumber'] = substr($line,7,35);
			$pharmacy['ReferenceNumberAlt1'] = substr($line,42,35);
			$pharmacy['ReferenceNumberAlt1Qualifier'] = substr($line,77,3);
			$pharmacy['StoreName'] = substr($line,80,35);
			$pharmacy['AddressLine1'] = substr($line,115,35);
			$pharmacy['AddressLine2'] = substr($line,150,35);
			$pharmacy['City'] = substr($line,185,35);
			$pharmacy['State'] = substr($line,220,2);
			$pharmacy['Zip'] = substr($line,222,11);
			$pharmacy['PhonePrimary'] = substr($line,233,25);
			$pharmacy['Fax'] = substr($line,258,25);
			$pharmacy['Email'] = substr($line,283,80); 
			$pharmacy['PhoneAlt1'] = substr($line,363,25);
			$pharmacy['PhoneAlt1Qualifier'] = substr($line,388,3);
			$pharmacy['PhoneAlt2'] = substr($line,391,25);
			$pharmacy['PhoneAlt2Qualifier'] = substr($line,416,3);
			$pharmacy['PhoneAlt3'] = substr($line,419,25);
			$pharmacy['PhoneAlt3Qualifier'] = substr($line,444,3);
			$pharmacy['PhoneAlt4'] = substr($line,447,25);
			$pharmacy['PhoneAlt4Qualifier'] = substr($line,472,3);
			$pharmacy['PhoneAlt5'] = substr($line,475,25);
			$pharmacy['PhoneAlt5Qualifier'] = substr($line,500,3);
			$pharmacy['ActiveStartTime'] = substr($line,503,22);
			$pharmacy['ActiveEndTime'] = substr($line,525,22);
			$pharmacy['ServiceLevel'] = substr($line,547,5);
			$pharmacy['PartnerAccount'] = substr($line,552,35);
			$pharmacy['LastModifiedDate'] = substr($line,587,22);
			$pharmacy['TwentyFourHourFlag'] = substr($line,609,1);
			$pharmacy['Available CrossStreet'] = substr($line,610,35);
			$pharmacy['RecordChange'] = substr($line,645,1);
			$pharmacy['OldServiceLevel'] = substr($line,646,5); 
			$pharmacy['TextServiceLevel'] = substr($line,651,100);
			$pharmacy['TextServiceLevelChange'] = substr($line,751,100);
			$pharmacy['Version'] = substr($line,851,5);
			$pharmacy['NPI'] = substr($line,856,10);
			$data = array();
			foreach ($pharmacy as $key=>$value) {
				$data[$key] = trim($value);
			}
			$p = new Pharmacy();
			$p->_shouldAudit = false;
			$p->populateWithArray($data);
			if (isset($pharmacies[$p->NCPDPID])) {
				$p->pharmacyId = $pharmacies[$p->NCPDPID]['pharmacyId'];
				$p->preferred = $pharmacies[$p->NCPDPID]['preferred'];
			}
			//$p->populatePharmacyIdWithNCPDPID();
			$p->persist();
			$counter++;
		}
		fclose($pharmDataTmp);
		unlink($filename);

		trigger_error('Number of rows updated: '.$counter,E_USER_NOTICE);
		trigger_error('after loading pharmacies: '.calcTS(),E_USER_NOTICE);
		return $counter;
	}

}
