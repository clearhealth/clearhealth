<?php
/*****************************************************************************
*       GeneralEncryption.php
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


class GeneralEncryption {

	// Encrypt and Decrypt AES 256
	public static function encryptAES256($value,$passphrase) {
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB),MCRYPT_RAND);
		$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$passphrase,$value,MCRYPT_MODE_ECB,$iv);
		return trim(base64_encode($encrypted));
	}

	public static function decryptAES256($value,$passphrase) {
		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($ivSize,MCRYPT_RAND);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$passphrase,base64_decode($value),MCRYPT_MODE_ECB,$iv);
		return trim($decrypted);
	}

}
