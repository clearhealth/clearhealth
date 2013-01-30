<?php
/*****************************************************************************
*       ProblemList.php
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


class ProblemList extends WebVista_Model_ORM implements NSDRMethods {

	protected $problemListId;
	protected $code;
	protected $codeTextShort;
	protected $dateOfOnset;
	protected $service;
	protected $personId;
	protected $person;
	protected $providerId;
	protected $provider;
	protected $status;
	protected $immediacy;
	protected $problemListComments = array();
	protected $lastUpdated;
	protected $flags;
	protected $previousStatus;
	protected $_table = "problemLists";
	protected $_primaryKeys = array("problemListId");
	protected $_cascadePersist = false;

	public function __construct() {
		$this->person = new Person();
		$this->person->_cascadePersist = false;
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
		parent::__construct();
	}

	public function addComment(Array $comment) {
		$problemListComment = new ProblemListComment();
		$problemListComment->populateWithArray($comment);
		$this->problemListComments[] = $problemListComment;
	}

	public function setProblemListComments(Array $comments) {
		$problemListComments = array();
		foreach ($this->problemListComments as $comment) {
			$problemListComments[$comment->problemListCommentId] = $comment;
		}
		$this->problemListComments = array();
		foreach($comments as $comment) {
			$problemListComment = new ProblemListComment();
			$problemListComment->populateWithArray($comment);
			// we need to populate person
			// if not populated, this may alter the person entry (don't know why?)
			$problemListComment->author->populate();
			$this->problemListComments[] = $problemListComment;
		}
	}

	public function setProblemListId($id) {
		$this->problemListId = (int)$id;
		foreach ($this->problemListComments as $comment) {
			$comment->problemListId = $this->problemListId;
		}
	}

	public function populateWithArray($array) {
		parent::populateWithArray($array);
		$this->populateDependents();
	}

	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from(array('pl'=>'problemLists'))
			       ->joinLeft(array('plc'=>'problemListComments'),"pl.problemListId=plc.problemListId",array('problemListCommentId','date','authorId','comment'))
			       ->where('pl.problemListId = ?',(int)$this->problemListId);
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$rows = $db->fetchAll($dbSelect);
		$parentPopulated = false;
		foreach ($rows as $row) {
			if (!$parentPopulated) {
				$parentPopulated = true;
				$this->populateWithArray($row);
			}
			if ((int)$row['problemListCommentId'] > 0) {
				$this->addComment($row);
			}
		}
		$this->postPopulate();
		return isset($rows[0]);
	}

	public function persist() {
		parent::persist();
		$db = Zend_Registry::get('dbAdapter');
		// remove all comments to a particular problem list from db
		$sql = "DELETE FROM `problemListComments` WHERE `problemListId`='{$this->problemListId}'";
		$db->query($sql);
		// add comments one by one
		foreach ($this->problemListComments as $comment) {
			$comment->persist();
		}
	}

	protected function populateDependents() {
		$this->person->person_id = $this->personId;
		$this->person->populate();
		$this->provider->setPersonId($this->providerId);
		$this->provider->populate();
	}

	public function nsdrPersist($tthis,$context,$data) {
		return true;
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$ret = array();
		return $ret;
	}

	public function nsdrMostRecent($tthis,$context,$data) {
		$ret = array();
		return $ret;
	}

}
