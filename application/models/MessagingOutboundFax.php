<?php
/*****************************************************************************
*       MessagingOutboundFax.php
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


class MessagingOutboundFax extends WebVista_Model_ORM {

	protected $messagingId;
	protected $auditId;
	protected $transmissionId;
	protected $docid;
	protected $faxNumber;
	protected $resend;
	protected $retries;
	protected $finalDisposition;
	protected $faxStatus;
	protected $faxStatusDescription;
	protected $faxNum;
	protected $dateCompletion;
	protected $recipientCSID;
	protected $duration;
	protected $pagesSent;
	protected $numberOfRetries;

	protected $_table = 'messagingOutboundFaxes';
	protected $_primaryKeys = array('messagingId');

	public function checkFinalDisposition() {
		if ($this->finalDisposition > 0) {
			return false;
		}
		$url = Zend_Registry::get('config')->healthcloud->eFax->Url;
		$url .= '/check-disposition?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,array('transmissionId'=>$this->transmissionId));
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch);
		trigger_error('RESPONSE: '.$output,E_USER_NOTICE);
		try {
			$xml = new SimpleXMLElement($output);
			if (isset($xml->transmissionId)) {
				//$this->transmissionId = (string)$xml->transmissionId;
				//$this->docid = (string)$xml->docid;
				$this->faxStatus = (string)$xml->faxStatus;
				$this->faxStatusDescription = (string)$xml->faxStatusDescription;
				$this->faxNum = (string)$xml->faxNumber;
				$this->dateCompletion = (string)$xml->dateCompletion;
				$this->recipientCSID = (string)$xml->recipientCSID;
				$this->duration = (string)$xml->duration;
				$this->pagesSent = (string)$xml->pagesSent;
				$this->numberOfRetries = (string)$xml->numberOfRetries;
				$this->finalDisposition = 1;
				$this->persist();
			}
			else if (isset($xml->error)) {
				trigger_error('Error: '.(string)$xml->error,E_USER_NOTICE);
			}
		}
		catch (Exception $e) {
			trigger_error('Error: '.$e->getMessage(),E_USER_NOTICE);
		}
		curl_close($ch);
	}

	public function getMessagingOutboundFaxId() {
		return $this->messagingId;
	}

}
