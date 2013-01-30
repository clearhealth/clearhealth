<?php
/*****************************************************************************
*       Attachment.php
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


class Attachment extends WebVista_Model_ORM {
	protected $attachmentId;
	protected $attachmentReferenceId;
	protected $name;
	protected $dateTime;
	protected $mimeType;
	protected $md5sum;

	protected $_table = "attachments";
	protected $_primaryKeys = array("attachmentId");

	public function populateWithAttachmentReferenceId() {
                $db = Zend_Registry::get('dbAdapter');
                $sql = "SELECT * from " . $this->_table . " WHERE 1 "
                  . " and attachmentReferenceId = " . $db->quote($this->attachmentReferenceId) . " order by dateTime DESC limit 1";
                $this->populateWithSql($sql);
        }

	public function populateWithMd5sum() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('md5sum = ?',$this->md5sum)
				->order('dateTime DESC')
				->limit(1);
		$this->populateWithSql($sqlSelect->__toString());
	}

	public function getIteratorByAttachmentReferenceId($attachmentReferenceId = null) {
		if ($attachmentReferenceId === null) {
			$attachmentReferenceId = $this->attachmentReferenceId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('attachmentReferenceId = ' . $attachmentReferenceId)
				->order('dateTime DESC');
		return $this->getIterator($sqlSelect);
	}

	public function getRawData() {
		$data = '';
		if ($this->attachmentId > 0) {
			$db = Zend_Registry::get('dbAdapter');
			$sql = 'SELECT data FROM attachmentBlobs WHERE attachmentId = '.(int)$this->attachmentId;
			$stmt = $db->query($sql);
			if ($row = $stmt->fetch()) {
				$data = $row['data'];
			}
			$stmt->closeCursor();
		}
		return $data;
	}

}
