<?php
/*****************************************************************************
*       BarcodeMacro.php
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


class BarcodeMacro extends WebVista_Model_ORM {
	protected $name;
	protected $regex;
	protected $macro;
	protected $active;
	protected $cache;
	protected $order;
	protected $_table = "barcodeMacros";
	protected $_primaryKeys = array("name");

	/**
	 * Overrides WebVista_Model_ORM::populate()
	 */
	public function populate() {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where("name = ?",preg_replace('/[^0-9a-z_A-Z- \.]/','',$this->name));
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	/**
	 * Overrides WebVista_Model_ORM::persist()
	 */
	public function persist() {
		$order = (int)$this->order;
		if (!$order > 0 && $this->_persistMode != WebVista_Model_ORM::DELETE) {
			$db = Zend_Registry::get("dbAdapter");
			$dbSelect = $db->select()
				       ->from($this->_table,"MAX(`order`) AS order");
			$result = $db->fetchRow($dbSelect);
			$this->order = $result['order'] + 1;
		}
		parent::persist();
	}

	/**
	 * Reorder macro
	 */
	public function reorder($from,$to) {
		$from = preg_replace('/[^0-9a-z_A-Z- \.]/','',$from);
		$to = preg_replace('/[^0-9a-z_A-Z- \.]/','',$to);
		$barcodeMacroFrom = new self();
		$barcodeMacroFrom->name = $from;
		$barcodeMacroFrom->populate();
		if (!strlen($barcodeMacroFrom->macro) > 0) {
			return;
		}

		$barcodeMacroTo = new self();
		$barcodeMacroTo->name = $to;
		$barcodeMacroTo->populate();
		if (!strlen($barcodeMacroTo->macro) > 0) {
			return;
		}

		$db = Zend_Registry::get("dbAdapter");
		$db->beginTransaction();
		try {
			$orderFrom = $barcodeMacroFrom->order;
			$barcodeMacroFrom->order = $barcodeMacroTo->order;
			$barcodeMacroFrom->persist();
			$barcodeMacroTo->order = $orderFrom;
			$barcodeMacroTo->persist();
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			trigger_error($e->getMessage(),E_USER_NOTICE);
		}
	}

	public function getBarcodeMacroId() {
		return $this->name;
	}

}
