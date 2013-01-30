<?php
/*****************************************************************************
*       PostingJournal.php
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


class PostingJournal extends WebVista_Model_ORM {

	protected $postingJournalId;
	protected $paymentId;
	protected $payment;
	protected $patientId;
	protected $payerId;
	protected $visitId;
	protected $claimLineId;
	protected $claimFileId;
	protected $amount;
	protected $note;
	protected $userId;
	protected $datePosted;
	protected $dateTime;

	protected $_table = 'postingJournals';
	protected $_primaryKeys = array('postingJournalId');
	protected $_cascadePersist = false;

	public function __construct() {
		$this->payment = new Payment();
		$this->payment->_cascadePersist = false;
	}

	public function setPaymentId($id) {
		$this->paymentId = (int)$id;
		$this->payment->paymentId = $this->paymentId;
	}

	public function getEnteredBy() {
		$ret = '';
		$userId = (int)$this->userId;
		if ($userId > 0) {
			$user = new User();
			$user->userId = $userId;
			$user->populate();
			$ret = $user->username;
		}
		return $ret;
	}

}
