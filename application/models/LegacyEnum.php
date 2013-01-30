<?php
/*****************************************************************************
*       LegacyEnum.php
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


class LegacyEnum extends WebVista_Model_ORM {

	protected $enumeration_value_id;
	protected $enumeration_id;
	protected $guid;
	protected $systemName;
	protected $key;
	protected $value;
	protected $sort;
	protected $extra1;
	protected $extra2;
	protected $status;
	protected $depth;
	protected $parent_id;

	protected $_table = "enumeration_value";
	protected $_primaryKeys = array('enumeration_value_id');

	static public function getIterByEnumerationId($enumerationId) {
		$enumerationId = (int) $enumerationId;
		$enumeration = new LegacyEnum();
		$db = Zend_Registry::get('dbAdapter');
		$enumSelect = $db->select()
			->from($enumeration->_table)
			->where('enumeration_id = ' . $enumerationId);
		$iter = $enumeration->getIterator($enumSelect);
		return $iter;
		
	}

	static public function getIterByEnumerationName($name) {
                $enumeration = new LegacyEnum();
                $db = Zend_Registry::get('dbAdapter');
                $enumSelect = $db->select()
                        ->from($enumeration->_table)
                        ->where('enumeration_id = (select enumeration_id from enumeration_definition where name=' . $db->quote($name) .')');
                $iter = $enumeration->getIterator($enumSelect);
                return $iter;

        }
	static public function getEnumArray($name,$key = "key", $value = "value") {
                $iter = LegacyEnum::getIterByEnumerationName($name);
                return $iter->toArray($key, $value);

        }
}
