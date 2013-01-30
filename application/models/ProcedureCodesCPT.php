<?php
/*****************************************************************************
*       ProcedureCodesCPT.php
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


class ProcedureCodesCPT extends WebVista_Model_ORM {

	protected $code;
	protected $textShort;
	protected $textLong;
	protected $_table = "procedureCodesCPT";
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

	public function ormVisitTypeEditMethod($ormId,$isAdd) {
		return $this->ormEditMethod($ormId,$isAdd);
	}

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['ormId'] = $ormId;
		}
		return $view->action('edit-type','visit-details',null,$params);
	}

	public function getProcedureCodesCPTId() {
		return $this->code;
	}

}
