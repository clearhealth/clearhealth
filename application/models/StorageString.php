<?php
/*****************************************************************************
*       StorageString.php
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


class StorageString extends WebVista_Model_ORM {

	protected $foreign_key;
	protected $value_key;
	protected $value;
	protected $array_index; // should start at index 1 otherwise the persist will get the next sequence id

	protected $_table = 'storage_string';
	protected $_primaryKeys = array('foreign_key','value_key','array_index');
	protected $_legacyORMNaming = true;

	public function getStorageStringId() {
		return $this->foreign_key;
	}

}
