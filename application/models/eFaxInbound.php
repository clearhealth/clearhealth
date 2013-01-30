<?php
/*****************************************************************************
*       eFaxInbound.php
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


class eFaxInbound extends WebVista_Model_ORM {

	protected $eFaxInboundId;
	protected $dateRequested;
	protected $requestType;
	protected $accountId;
	protected $numberDialed;
	protected $dateReceived;
	protected $faxName;
	protected $fileType;
	protected $pageCount;
	protected $CSID;
	protected $ANI;
	protected $status;
	protected $MCFID;
	protected $fileContents;

	protected $_table = 'eFaxInbounds';
	protected $_primaryKeys = array('eFaxInboundId');

	public function getLatestDateReceived() {
		$dateReceived = false;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->order('dateReceived DESC')
				->limit(1);
		if ($row = $db->fetchRow($sqlSelect)) {
			$dateReceived = $row['dateReceived'];
		}
		return $dateReceived;
	}

	public function checkInbounds() {
		$ctr = 0;
		$url = Zend_Registry::get('config')->healthcloud->eFax->getInboundUrl;
		$url .= '?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		$data = array();
		$dateTime = $this->getLatestDateReceived();
		if ($dateTime !== false) {
			$data = array('dateTime'=>$dateTime);
		}
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch);
		trigger_error('RESPONSE: '.$output,E_USER_NOTICE);
		try {
			$xml = new SimpleXMLElement($output);
			if (isset($xml->inbounds)) {
				$inbounds = $xml->inbounds;
				foreach ($inbounds->inbound as $item) {
					$inbound = new self();
					$inbound->dateRequested = (string)$item->dateRequested;
					$inbound->requestType = (string)$item->requestType;
					$inbound->accountId = (string)$item->accountId;
					$inbound->numberDialed = (string)$item->numberDialed;
					$inbound->dateReceived = (string)$item->dateReceived;
					$inbound->faxName = (string)$item->faxName;
					$inbound->fileType = (string)$item->fileType;
					$inbound->pageCount = (string)$item->pageCount;
					$inbound->CSID = (string)$item->CSID;
					$inbound->ANI = (string)$item->ANI;
					$inbound->status = (string)$item->status;
					$inbound->MCFID = (string)$item->MCFID;
					$inbound->fileContents = (string)$item->fileContents;
					$inbound->persist();
					$ctr++;
				}
			}
			else if (isset($xml->error)) {
				trigger_error('Error: '.(string)$xml->error,E_USER_NOTICE);
			}
		}
		catch (Exception $e) {
			trigger_error('Error: '.$e->getMessage(),E_USER_NOTICE);
		}
		curl_close($ch);
		return $ctr;
	}

}
