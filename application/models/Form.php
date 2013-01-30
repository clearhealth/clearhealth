<?php
/*****************************************************************************
*       Form.php
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


class Form extends WebVista_Model_ORM {
	protected $form_id;
	protected $name;
	protected $description;
	protected $system_name;
	protected $_primaryKeys = array('form_id');
	protected $_table = "form";
	protected $_legacyORMNaming = true;

    public function getFormList() {
        $db = Zend_Registry::get('dbAdapter');
        $select = $db->select();
        $select->from('form',array('form_id', 'name'))
               ->order(array('name ASC'));

        $ret = array();
        if ($rowset = $db->fetchAll($select)) {
            $ret = $rowset;
        }
        return  $ret;
    }
}
