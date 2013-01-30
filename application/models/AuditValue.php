<?php
/*****************************************************************************
*       AuditValue.php
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


class AuditValue extends WebVista_Model_ORM {
	protected $auditValueId;
	protected $auditId;
	protected $key;
	protected $value;
	protected $_table = "auditValues";
	protected $_primaryKeys = array('auditValueId');
	protected $_persistMode = WebVista_Model_ORM::INSERT;
	protected $_ormPersist = false;

	public function persist() {
		if ($this->_ormPersist) {
			return parent::persist();
		}
		if ($this->shouldAudit()) {
			$sql = $this->toSQL();
			AuditLog::appendSql($sql);
		}
	}
}
