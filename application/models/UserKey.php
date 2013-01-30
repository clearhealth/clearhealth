<?php
/*****************************************************************************
*       UserKey.php
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


class UserKey extends WebVista_Model_ORM {
	protected $userId;
	protected $privateKey;
	protected $publicKey;
	protected $iv;

	protected $_config;
	protected $_table = "userKeys";
	protected $_primaryKeys = array("userId");
	
	public function __construct() {
		parent::__construct();
		$this->_config = Zend_Registry::get('config')->user->pki;
	}

	function generateKeys($passphrase) {
		$identity = Zend_Auth::getInstance()->getIdentity();
		$dn = array(
				"countryName" => $this->_config->countryName, 
				"stateOrProvinceName" => $this->_config->stateOrProvinceName, 
				"localityName" => $this->_config->localityName, 
				"organizationName" => $this->_config->organizationName, 
				"organizationalUnitName" => $this->_config->organizationUnitName, 
				"commonName" =>  $identity->firstName . " " . $identity->lastName . "(" . $identity->username . ")", 
				"emailAddress" => $this->_config->emailAddress
		);

                $privkey = openssl_pkey_new();
                $csr = openssl_csr_new($dn, $privkey);
                $sscert = openssl_csr_sign($csr, null, $privkey, $this->_config->numberOfDays);
                openssl_x509_export($sscert, $publickey);
                openssl_pkey_export($privkey, $privatekey);
                openssl_csr_export($csr, $csrStr);
		$this->publicKey = $publickey;
		$this->privateKey = $this->_encryptPrivateKey($privatekey,$passphrase);
	}
	
	/*
	 * OpenSSL PHP does not support secure encryption (non 3des) of 
	 * the private key so we implement AES encryption using mcrypt here.
	 *
	 */
	protected function _encryptPrivateKey($data,$passphrase) {
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
		$key = hash("sha256", $passphrase, true);
		//to be AES256 we need MCRYPT_RIJNDAEL_128 with 32 byte key, note RIJNDAEL != AES, AES256 = RIJNDAEL_128 with 16 byte iv and 32 byte key
		$iv = mcrypt_create_iv(16);
		$this->iv = base64_encode($iv);
		mcrypt_generic_init($cipher, $key, $iv);
		$cipherText = mcrypt_generic($cipher,$data);
                mcrypt_generic_deinit($cipher);
		return base64_encode($cipherText);
	}

	public function getDecryptedPrivateKey($passphrase) {
		$key = hash("sha256",$passphrase,true);
		$plainText = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($this->privateKey), MCRYPT_MODE_CBC, base64_decode($this->iv));
		//trigger_error($plainText, E_USER_NOTICE);
		if (substr($plainText,0,10) != "-----BEGIN") {
			throw new Exception("Key could not be retrieved, this is usually due to an incorrect passphrase. Please try again.");
		}
		return rtrim($plainText, "\0");
	}

	public function getUserKeyId() {
		return $this->userId;
	}

}
