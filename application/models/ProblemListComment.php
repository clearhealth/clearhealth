<?php
/*****************************************************************************
*       ProblemListComment.php
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


class ProblemListComment extends WebVista_Model_ORM {

	protected $problemListCommentId;
	protected $problemListId;
	protected $date;
	protected $authorId;
	protected $author;
	protected $comment;
	protected $_table = "problemListComments";
	protected $_primaryKeys = array("problemListCommentId");

	public function __construct() {
		$this->author = new Person();
		parent::__construct();
	}

	public function setAuthorId($authorId) {
		$authorId = (int)$authorId;
		if ($this->author->person_id > 0 && $authorId != $this->authorId) {
			$author = new Person();
			unset($this->author);
			$this->author = $author;
		}
		$this->authorId = $authorId;
		$this->author->person_id = $authorId;
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->author->ORMFields())) {
			return $this->author->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->author->__get($key))) {
			return $this->author->__get($key);
		}
		return parent::__get($key);
	}

}
