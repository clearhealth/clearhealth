<?php
/*****************************************************************************
*       eFax.php
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


class eFax {

	protected $url = '';
	protected $accountIdentifier = '';
	protected $username = '';
	protected $password = '';

	protected $errors = array();
	protected $response;

	public static $apiKey = '';

	public function __construct($username = null, $password = null, $accountIdentifier = null, $url = null) {
		$eFax = Zend_Registry::get('config')->healthcloud->eFax;
		if ($username === null) {
			$username = $eFax->username;
		}
		$this->setUsername($username);
		if ($password === null) {
			$password = $eFax->password;
		}
		$this->setPassword($password);
		if ($accountIdentifier === null) {
			$accountIdentifier = $eFax->accountIdentifier;
		}
		$this->setAccountIdentifier($accountIdentifier);
		if ($url === null) {
			$url = $eFax->Url;
		}
		$this->setUrl($url);
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setAccountIdentifier($accountIdentifier) {
		$this->accountIdentifier = $accountIdentifier;
	}

	public function getAccountIdentifier() {
		return $this->accountIdentifier;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function getPassword() {
		return $this->password;
	}

	public function addError($error) {
		$this->errors[] = $error;
	}

	public function getErrors() {
		return $this->errors;
	}

	public function setErrors($errors) {
		$this->errors = $errors;
	}

	public function getResponse() {
		return $this->response;
	}

	public function setResponse($response) {
		$this->response = $response;
	}

	protected function preCurlExec($ch) {
	}

	protected function transmit($data) {
		if (is_array($data)) {
			$data = http_build_query($data);
		}
		file_put_contents('/tmp/efax.transmit',$this->getUrl().': '.$data,FILE_APPEND);
		$ch = curl_init($this->getUrl());
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$this->_preCurlExec($ch);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

}
