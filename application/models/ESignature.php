<?php
/*****************************************************************************
*       ESignature.php
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


class ESignature extends WebVista_Model_ORM {
	
	protected $eSignatureId;
	protected $eSignatureParentId;
	protected $dateTime;
	protected $signedDateTime = '0000-00-00 00:00:00';
	protected $signingUserId;
	protected $objectId;
	protected $objectClass;
	protected $summary;
	protected $signature;
	
	protected $_table = "eSignatures";
	protected $_primaryKeys = array ("eSignatureId");

	function __construct() {
                parent::__construct();
        }

	public static function createSignatureEntry(Document $object,$signingUserId = null) {
		$db = Zend_Registry::get('dbAdapter');
		$esig = new ESignature();
		if ($object->eSignatureId > 0) {
			//document is already signed, we cannot create a new signature entry for an already signed document unless the document is first unlinked
			return false;
		}
		$esigSelect = $db->select()
			->from('eSignatures')
			->where('eSignatures.objectId = ' . (int)$object->documentId)
			->order('eSignatureId DESC');
		if (($row = $db->query($esigSelect)->fetch()) !== false) {
			if ($row['signature'] == "") {
				//open signature record exists so do not create another
				return false;
			}
                	$esig->editedSummary = $object->getSummary();
		}
		else {
                	$esig->unsignedSummary = $object->getSummary();
		}

                $esig->objectId = $object->getDocumentId();
                $esig->objectClass = get_class($object);
		if ($signingUserId === null) {
                	$signingUserId = Zend_Auth::getInstance()->getIdentity()->personId;
		}
                $esig->signingUserId = (int)$signingUserId;
                $esig->dateTime = date('Y-m-d H:i:s');
                $esig->persist();
	}

	public function setUnsignedSummary($string) {
		 $this->summary = substr($string,0,200) . " **Unsigned**";
	}

	public function setEditedSummary($string) {
                 $this->summary = substr($string,0,200) . " **Edited**";
        }

	public function sign(Document $object, $passphrase) {
		$document = $object->toDocument();
		$hash = hash('sha256',$this->signedDateTime . " " . $document);
		$userKey = new UserKey();
		$userKey->userId = $this->signingUserId;
		$userKey->populate();

		$privateKeyString = $userKey->getDecryptedPrivateKey($passphrase);
		$privateKey = openssl_pkey_get_private($privateKeyString);

		openssl_private_encrypt($hash,$signedHash,$privateKey);
		$this->signature = base64_encode($signedHash);
		openssl_free_key($privateKey);
	}

	public function verify(Document $object,$signature) {
		$document = $object->toDocument();
                $hash = hash('sha256',$this->signedDateTime . " " . $document);
		$userKey = new UserKey();
                $userKey->userId = $this->signingUserId;
                $userKey->populate();
		$publicKey = openssl_get_publickey($userKey->publicKey);
		openssl_public_decrypt(base64_decode($signature), $verifyHash, $publicKey);
		openssl_free_key($publicKey);
		if ($hash === $verifyHash)  return true;
		throw new Exception('Document verification with signature failed.');
	}

	public function populateByObject($objectClass=null,$objectId=null) {
		if ($objectClass === null) {
			$objectClass = $this->objectClass;
		}
		if ($objectId === null) {
			$objectId = $this->objectId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('objectClass = ?',$objectClass)
				->where('objectId = ?',$objectId);
		$this->populateWithSql($sqlSelect->__toString());
	}

	public static function retrieveSignatureId($objectClass,$objectId,$signed=true) {
		$db = Zend_Registry::get('dbAdapter');
		$esig = new self();
		$sqlSelect = $db->select()
				->from($esig->_table)
				->where('objectClass = ?',$objectClass)
				->where('objectId = ?',$objectId)
				->limit(1);
		if ($signed) {
			$sqlSelect->where("signature != ''");
		}
		$esig->populateWithSql($sqlSelect->__toString());
		return $esig->eSignatureId;
	}

}
