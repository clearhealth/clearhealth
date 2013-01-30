<?php
/*****************************************************************************
*       AdminUsersController.php
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


class AdminUsersController extends WebVista_Controller_Action {

	protected $_form;
	protected $_user;
	
	public function editAction() {
		$personId = (int)$this->_getParam('personId');
        	if (isset($this->_session->messages)) {
        	    $this->view->messages = $this->_session->messages;
        	}
		$this->_form = new WebVista_Form(array('name' => 'user-detail'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "admin-users.raw/edit-process");
		$this->_user = new User();
		$this->_user->personId = $personId;
		$this->_user->populateWithPersonId();
		$this->_form->loadORM($this->_user, "User");
		//var_dump($this->_form);
		$this->view->form = $this->_form;
		$this->view->user = $this->_user;

		$permissionTemplate = new PermissionTemplate();
		$permissionTemplateIterator = $permissionTemplate->getIterator();
		$permissionTemplates = $permissionTemplateIterator->toArray('permissionTemplateId','name');
		$permissionTemplates['superadmin'] = 'Super Administrator';
		$this->view->permissionTemplates = $permissionTemplates;
        	$this->render('edit-user');
	}

	public function editProcessAction() {
		$personId = (int)$this->_getParam('personId');
		$params = $this->_getParam('user');
		$this->_user = new User();
		$this->_user->personId = $personId;
		$this->_user->populateWithPersonId();
		$this->_user->populateWithArray($params);
		$this->_user->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$msg = "Record Saved for User: " . ucfirst($this->_user->username);
		$json->direct($msg);
	}

	public function changePasswordAction() {
		$this->render('change-password');
	}

	public function processChangePasswordAction() {
		$params = $this->_getParam('user');
		$currentUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$user = new User();
		$user->userId = $currentUserId;
		$user->personId = $currentUserId; // userId and personId are similar
		$user->populate();
		if ($params['newPassword'] != $params['confirmNewPassword']) {
			$ret = __('New password does not match confirmed password.');
		}
		else if ($user->password != $params['currentPassword']) {
			$ret = __('Current password is invalid.');
		}
		else if (!strlen($params['newPassword']) > 0) {
			$ret = __('New password is required.');
		}
		else if ($params['newPassword'] == $params['currentPassword']) {
			$ret = __('New password must be different from current password.');
		}
		else {
			$password = $params['newPassword'];
			$user->password = $password;
			try {
				$user->persist();
				$ret = true;
			}
			catch (Exception $e) {
				$ret = $e->getMessage();
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function editSigningKeyAction() {
		$currentUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$userKey = new UserKey();
		$userKey->userId = $currentUserId;
		$userKey->populate();
		$isNewKey = true;
		if (strlen($userKey->privateKey) > 0) {
			$isNewKey = false;
		}
		$this->view->isNewKey = $isNewKey;
		$this->render('edit-signing-key');
	}

	public function processEditSigningKeyAction() {
		$params = $this->_getParam('user');
		if ($params['newSignature'] != $params['confirmNewSignature']) {
			$ret = __('New signature does not match confirmed signature.');
		}
		else if (!strlen($params['newSignature']) > 0) {
			$ret = __('New signature is required.');
		}
		else if ($params['newSignature'] == $params['currentSignature']) {
			$ret = __('New signature must be different from current signature.');
		}
		else {
			$currentUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$userKey = new UserKey();
			$userKey->userId = $currentUserId;
			$userKey->populate();
			$newUserKey = clone $userKey;
			$newUserKey->generateKeys($params['newSignature']);
			do {
				if (strlen($userKey->privateKey) > 0) {
					try {
						$privateKeyString = $userKey->getDecryptedPrivateKey($params['currentSignature']);
					}
					catch (Exception $e) {
						$ret = __('Current signature is invalid.'.PHP_EOL.$e->getMessage());
						break;
					}
				}
				try {
					$newUserKey->persist();
					$ret = true;
				}
				catch (Exception $e) {
					$ret = $e->getMessage();
				}
			} while (false);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function validateSigningKeyAction() {
		$signature = $this->_getParam('signature');
		$currentUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$userKey = new UserKey();
		$userKey->userId = $currentUserId;
		$userKey->populate();
		if (strlen($userKey->privateKey) > 0) {
			try {
				$privateKeyString = $userKey->getDecryptedPrivateKey($signature);
				$ret = __('Current signature is valid.');
			}
			catch (Exception $e) {
				$ret = __('Current signature is invalid.'.PHP_EOL.$e->getMessage());
			}
		}
		else {
			$ret = __('Cannot verify, no signature exists');
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function editLocationAction() {
		$personId = (int)$this->_getParam('personId');
		$prescriber = new EPrescriber();
		$prescriberIterator = $prescriber->getIteratorByProviderId($personId);
		$prescriberList = array();
		foreach ($prescriberIterator as $p) {
			$prescriberList[] = $this->_generatePrescriberRowData($p);
		}
		$this->view->prescriberList = $prescriberList;
		$this->view->serviceLevels = Provider::getServiceLevelOptions();
		$this->view->personId = $personId;
		$this->view->facilityIterator = new FacilityIterator();

		$this->render('edit-location');
	}

	protected function _generatePrescriberRowData(EPrescriber $prescriber) {
		$ret = array();
		$ret['id'] = $prescriber->ePrescriberId;
		$ret['data'] = array();
		$ret['data'][] = (int)$prescriber->buildingId;
		$ret['data'][] = $prescriber->serviceLevel;
		$ret['data'][] = date('Y-m-d',strtotime($prescriber->dateActiveStart));
		$ret['data'][] = date('Y-m-d',strtotime($prescriber->dateActiveEnd));
		$ret['data'][] = $prescriber->SSID;
		return $ret;
	}

	public function processEditLocationAction() {
		$params = $this->_getParam('prescriber');
		$prescriber = new EPrescriber();
		$prescriber->ePrescriberId = (int)$params['ePrescriberId'];
		if ($prescriber->ePrescriberId > 0) {
			$prescriber->populate();
		}
		else {
			$prescriber->dateActiveStart = date('Y-m-d');
			$prescriber->dateActiveEnd = date('Y-m-d',strtotime('+1 year'));
			$prescriber->serviceLevel = 3;
		}
		$prescriber->populateWithArray($params);
		if (!$prescriber->serviceLevel > 0) {
			$prescriber->dateActiveEnd = date('Y-m-d');
		}
		$prescriber->persist();

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$data = $this->_generatePrescriberRowData($prescriber);
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteLocationAction() {
		$prescriber = new EPrescriber();
		$prescriber->ePrescriberId = (int)$this->_getParam('id');
		$data = false;
		if ($prescriber->ePrescriberId > 0) {
			$prescriber->setPersistMode(WebVista_Model_ORM::DELETE);
			$prescriber->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _syncLocation(EPrescriber $ePrescriber) {
		$ret = true;

		$dateActiveStart = strtotime($ePrescriber->dateActiveStart);
		$dateActiveEnd = strtotime($ePrescriber->dateActiveEnd);
		$serviceLevel = (int)$ePrescriber->serviceLevel;

		$personId = (int)$ePrescriber->providerId;
		$provider = new Provider();
		$provider->personId = $personId;
		$provider->populate();
		$provider->serviceLevel = $serviceLevel;

		if ($dateActiveStart > 0 && $dateActiveEnd > 0) {
			$provider->dateActiveStart = date('Y-m-d H:i:s',$dateActiveStart);
			$provider->dateActiveEnd = date('Y-m-d H:i:s',$dateActiveEnd);
		}

		$person = new Person();
		$person->personId = $personId;
		$person->populate();

		$practice = new Practice();
		$practice->practiceId = $person->primaryPracticeId;
		$practice->populate();

		$data = array();
		$data['deaNumber'] = $provider->deaNumber;
		$data['stateLicenseNumber'] = $provider->stateLicenseNumber;
		//$data['portalId'] = Zend_Registry::get('config')->sureScripts->portalId;
		//$data['accountId'] = Zend_Registry::get('config')->sureScripts->accountId;
		$data['clinicName'] = ''.$practice->name;
		$data['lastName'] = $person->lastName;
		$data['firstName'] = $person->firstName;
		$address = $ePrescriber->building;
		$data['addressLine1'] = $address->line1;
		$data['addressLine2'] = $address->line2;
		$data['addressCity'] = $address->city;
		$data['addressState'] = $address->state;
		$data['addressZipCode'] = $address->zipCode;
		$data['email'] = $person->email;

		$data['phones'] = array(
			array('number'=>PhoneNumber::autoFixNumber($address->phoneNumber),'type'=>'TE'),
			array('number'=>PhoneNumber::autoFixNumber($address->fax),'type'=>'FX'),
		);

		$data['specialtyCode'] = $provider->specialty;
		$specialtyQualifier = '';
		if (strlen($provider->specialty) > 0) {
			$specialtyQualifier = 'AM';
		}
		$data['specialtyQualifier'] = $specialtyQualifier;
		$data['serviceLevel'] = $provider->serviceLevel;

		$now = strtotime('now');
		$days30 = strtotime('+30 days',$now);
		$activeStartTime = gmdate("Y-m-d\TH:i:s.0",$now).'Z';
		$activeEndTime = gmdate("Y-m-d\TH:i:s.0",$days30).'Z';
		$data['activeStartTime'] = $provider->dateActiveStartZ;
		$data['activeEndTime'] = $provider->dateActiveEndZ;
		$dateActiveEnd = strtotime(date('Y-m-d',strtotime($provider->dateActiveEndZ)));
		if ($dateActiveEnd <= strtotime(date('Y-m-d'))) {
			// to disable a prescriber ActiveEndTime must be set to current date and ServiceLevel must be set to zero.
			$data['activeEndTime'] = date('Y-m-d');
			$data['serviceLevel'] = 0;
			$provider->serviceLevel = 0;
		}
		$provider->persist();
		$identifierType = $provider->identifierType;
		if (strlen($identifierType) > 0) {
			$data[$identifierType] = $provider->identifier;
		}

		$messaging = new Messaging();
		//$messaging->messagingId = '';
		$type = 'add';
		$messaging->messageType = 'AddPrescriber';
		if (strlen($ePrescriber->SSID) > 0) {
			$messaging->messageType = 'UpdatePrescriber';
			$data['SPI'] = $ePrescriber->SSID;
			$type = 'update';
		}
		else if (strlen($provider->sureScriptsSPI) > 0) {
			$messaging->messageType = 'AddPrescriberLocation';
			$data['SPI'] = substr($provider->sureScriptsSPI,0,-3);
			$type = 'addLocation';
		}
		// backupPortalId must be supplied if type is updateLocation
		$messaging->populate();
		//$messaging->objectId = '';
		//$messaging->objectClass = '';
		$messaging->status = 'Sending';
		$messaging->note = 'Sending prescriber data';
		$messaging->dateStatus = date('Y-m-d H:i:s');
		//$messaging->auditId = '';
		$messaging->persist();

		$query = http_build_query(array('type'=>$type,'data'=>$data));
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/edit-prescriber?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
		$output = curl_exec($ch);
		trigger_error('OUTPUT: '.$output,E_USER_NOTICE);
		$error = "";
		$prescriberSPI = '';
		$messaging->status = 'Sent';
		$messaging->note = 'Prescriber data sent';
		if (!curl_errno($ch)) {
			try {
				$responseXml = simplexml_load_string($output);
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
					trigger_error('There was an error enabling an ePresciber, Error code: '.$errorCode.' Error Message: '.$errorMsg,E_USER_NOTICE);
				}
				elseif (isset($responseXml->data)) {
					$xml = new SimpleXMLElement($responseXml->data);
					$prescriber = $xml->AddPrescriberResponse->Prescriber;
					//if ($type == 'addLocation') {
					if (isset($xml->AddPrescriberLocationResponse)) {
						$prescriber = $xml->AddPrescriberLocationResponse->Prescriber;
					}
					$prescriberSPI = (string)$prescriber->Identification->SPI;
					if (!strlen($prescriberSPI) > 0) {
						$error = 'Registration failed for location '.$ePrescriber->building->name;
					}
				}
				if (isset($responseXml->rawMessage)) {
					$messaging->rawMessage = base64_decode((string)$responseXml->rawMessage);
					$messaging->rawMessageResponse = base64_decode((string)$responseXml->rawMessageResponse);
				}
			}
			catch (Exception $e) {
				$error = __("There was an error connecting to HealthCloud to enable ePrescribing for this provider. Please try again or contact the system administrator.");
					trigger_error("There was an error enabling an ePresciber, the response couldn't be parsed as XML: " . $output, E_USER_NOTICE);
			}
		}
		else {
			$error = __("There was an error connecting to HealthCloud to enable ePrescribing for this provider. Please try again or contact the system administrator.");
			trigger_error("Curl error connecting to healthcare enabled an ePrescribe record: " . curl_error($ch),E_USER_NOTICE);
		}
		curl_close ($ch);
		if (strlen($error) > 0) {
			$messaging->status = 'Error';
			$messaging->note = $error;
			$ret = false;
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		if (strlen($error) > 0) {
			return $error;
		}
		if (!strlen($provider->sureScriptsSPI) > 0) { // handler of the first SPI
			$provider->sureScriptsSPI = $prescriberSPI;
			$provider->persist();
		}
		if ($type == 'add' || $type == 'addLocation') {
			$ePrescriber->SSID = $prescriberSPI;
			$ePrescriber->persist();
		}
		return $ret;
	}

	public function processSyncLocationAction() {
		$prescriber = new EPrescriber();
		$prescriber->ePrescriberId = (int)$this->_getParam('ePrescriberId');
		$prescriber->populate();
		$data = array();
		$result = $prescriber->ssCheck();
		if ($result !== true) {
			$data['error'] = $result;
		}
		if (!isset($data['error'])) {
			$result = $this->_syncLocation($prescriber);
			if ($result !== true) {
				$data['error'] = $result;
			}
			else {
				$data['SSID'] = $prescriber->SSID;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSyncAllLocationAction() {
		$providerId = (int)$this->_getParam('providerId');
		$prescriber = new EPrescriber();
		$prescriberIterator = $prescriber->getIteratorByProviderId($providerId);
		$data = array();
		$data['error'] = array();
		$data['SSID'] = array();
		$prescribers = array();
		foreach ($prescriberIterator as $p) {
			$prescribers[] = $p;
			$result = $p->ssCheck();
			if ($result !== true) {
				$data['error'][] = '*)'.$p->building->name.': '.$result;
			}
		}
		if (!isset($data['error'][0])) {
			foreach ($prescribers as $p) {
				$result = $this->_syncLocation($p);
				if ($result !== true) {
					$data['error'][] = '*)'.$p->building->name.': '.$result;
				}
				else {
					$data['SSID'][$p->ePrescriberId] = $p->SSID;
				}
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function ajaxGetContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render();
	}

	public function processAddAction() {
		$username = $this->_getParam('username');
		$user = new User();
		$user->username = $username;
		$user->personId = WebVista_Model_ORM::nextSequenceId();
		$response = true;
		if (User::communityEditionPlusEnabled()) { // new user
			$response = $user->healthCloudActivation();
		}
		if ($response === true) {
			$user->userId = $user->personId;
			$user->persist();
			$ret = $user->personId;
		}
		else {
			$ret = array('error'=>$response);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function autoCompleteAction() {
        	$match = $this->_getParam('name');
		$match = preg_replace('/[^a-zA-Z-0-9]/','',$match);
		$matches = array();
		if (!strlen($match) > 0) $this->_helper->autoCompleteDojo($matches);
		$db = Zend_Registry::get('dbAdapter');
		$match = $db->quote($match.'%');
		$sqlSelect = $db->select()
				->from('user')
				->joinUsing('person','person_id')
				->where('person.last_name LIKE '.$match)
				->orWhere('person.first_name LIKE '.$match)
				->orWhere('user.username LIKE '.$match)
				->order('person.last_name DESC')
				->order('person.first_name DESC');

		$rows = $db->fetchAll($sqlSelect);
		foreach ($rows as $row) {
			$matches[$row['person_id']] = $row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1) . ' (' . $row['username'] .")"; 
		}
        	$this->_helper->autoCompleteDojo($matches);
	}

}
