<?php
/*****************************************************************************
*       ProcedureCodesImmunization.php
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


class ProcedureCodesImmunization extends WebVista_Model_ORM {

	protected $code;
	protected $textShort;
	protected $textLong;
	protected $_table = "procedureCodesImmunization";
	protected $_primaryKeys = array("code");

	public function persist() {
		if ($this->_persistMode === WebVista_Model_ORM::DELETE) {
			$db = Zend_Registry::get('dbAdapter');
			$sql = $this->toSQL();
			$code = preg_replace('/[^a-z_0-9- \.]/i','',$this->code);
			$sql .= " AND `code` = '{$code}'";
			$db->query($sql);
			$this->postPersist();
			if ($this->shouldAudit()) {
				WebVista_Model_ORM::audit($this);
			}
		}
		else {
			parent::persist();
		}
		return $this;
	}

	public function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('`code` = ?',$this->code);
		$ret = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $ret;
	}

	public function getProcedureCodesImmunizationId() {
		return $this->code;
	}

}
