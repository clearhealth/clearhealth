<?php
/*****************************************************************************
*       HealthCloudRegistrationController.php
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


class HealthCloudRegistrationController extends WebVista_Controller_Action {

	public function registerAction() {
		$this->view->updateFileId = (int)$this->_getParam('updateFileId');
		$data = array();
		$users = array();
		$xml = new SimpleXMLElement('<clearhealth/>');
		$xml->addChild('apiKey',Zend_Registry::get('config')->healthcloud->apiKey);
		foreach (User::listActiveUsers() as $user) {
			$xmlUser = $xml->addChild('user');
			$xmlUser->addChild('userId',(int)$user->personId);
			$xmlUser->addChild('username',(string)$user->username);
		}
		$ch = curl_init();
		$url = Zend_Registry::get('config')->healthcloud->updateServerUrl.'/check-users';
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml->asXML());
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		try {
			$response = curl_exec($ch);
			if (curl_errno($ch)) throw new Exception(curl_error($ch));
			curl_close($ch);
			trigger_error($response);
			$responseXml = new SimpleXMLElement($response);
			if ($responseXml->error) throw new Exception($responseXml->error->errorMsg,$responseXml->error->errorCode);
			$enabledUsers = array();
			$disabledUsers = array();
			foreach ($responseXml->user as $user) {
				$userId = (int)$user->userId;
				$username = (string)$user->username;
				if ($user->enabled && $user->enabled == 1) $enabledUsers[$userId] = $username;
				else $disabledUsers[$userId] = $username;
			}
			$disabledUsersCtr = count($disabledUsers);
			if ($disabledUsersCtr > 0) {
				$pointsPerUser = (float)$responseXml->pointsPerUser;
				$points = (float)$responseXml->points;
				$ret = array();
				$ret[] = 'Enabling subscription will cost you '.number_format($pointsPerUser,0,'.',',').' points/user!';
				$ret[] = 'You currently have '.number_format($points,0,'.',',').' points in you account!';
				$ret[] = '';
				$ret[] = '***** Users to register *****';
				$ctr = 1;
				foreach ($disabledUsers as $userId=>$username) {
					$user = new User();
					$user->username = $username;
					$user->populateWithUsername();
					$ret[] = $ctr++.') '.$user->displayName.' ('.$username.')';
				}
				$ret[] = '';
				$totalPoints = $disabledUsersCtr * $pointsPerUser;
				if ($totalPoints > $points) {
					$ret[] = 'WARNING: You currently have insufficient points!';
					$ret[] = '';
					$data['insufficientPoints'] = true;
				}
				if (count($enabledUsers) > 0) {
					$ret[] = '';
					$ret[] = '***** Registered Users *****';
					$ctr = 1;
					foreach ($enabledUsers as $userId=>$username) {
						$user = new User();
						$user->username = $username;
						$user->populateWithUsername();
						$ret[] = $ctr++.') '.$user->displayName.' ('.$username.')';
					}
				}
				$data['data'] = implode("\n",$ret);
			}
			else {
				$data['ok'] = true;
			}
		}
		catch (Exception $e) {
			$error = $e->getMessage();
			trigger_error($error);
			$data['error'] = $error;
		}
		$this->view->data = $data;
		$this->render('register');
	}

	public function processRegistrationAction() {
		$updateFileId = (int)$this->_getParam('updateFileId');
		$data = array();
		$users = array();
		$xml = new SimpleXMLElement('<clearhealth/>');
		$xml->addChild('apiKey',Zend_Registry::get('config')->healthcloud->apiKey);
		$xml->addChild('authorizingUserId',(int)Zend_Auth::getInstance()->getIdentity()->personId);
		$xml->addChild('authorizingUser',Zend_Auth::getInstance()->getIdentity()->username);
		foreach (User::listActiveUsers() as $user) {
			$xmlUser = $xml->addChild('user');
			$xmlUser->addChild('userId',(int)$user->personId);
			$xmlUser->addChild('username',(string)$user->username);
		}
		$ch = curl_init();
		$url = Zend_Registry::get('config')->healthcloud->updateServerUrl.'/activate-users';
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml->asXML());
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		try {
			$response = curl_exec($ch);
			if (curl_errno($ch)) throw new Exception(curl_error($ch));
			curl_close($ch);
			trigger_error($response);
			$responseXml = new SimpleXMLElement($response);
			if ($responseXml->error) throw new Exception((string)$responseXml->error->errorMsg,(string)$responseXml->error->errorCode);
			$data['data'] = (string)$responseXml->response;
			$updateFile = new UpdateFile();
			$updateFile->updateFileId = $updateFileId;
			$updateFile->populate();
			$updateFile->install();
		}
		catch (Exception $e) {
			$error = $e->getMessage();
			trigger_error($error);
			$data['error'] = $error;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}

