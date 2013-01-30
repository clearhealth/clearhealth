<?php
/*****************************************************************************
*       ConfigItem.php
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


class ConfigItem extends WebVista_Model_ORM {
    protected $configId;
    protected $value;

    protected $_table = "config";
    protected $_primaryKeys = array("configId");


    /**
     * Override parent populate implementation
     */
    public function populate() {
        $sql = "SELECT * from " . $this->_table . " WHERE 1 ";
        $doPopulate = false;
        foreach($this->_primaryKeys as $key) {
            $doPopulate = true;
            $value = preg_replace('/[^a-z_0-9-]/i','',$this->$key);
            $sql .= " and $key = '{$value}'";
        }
        if ($doPopulate == false) return false;
        $retval = false;
        $retval = $this->populateWithSql($sql);
        $this->postPopulate();
        return $retval;
    }

	public function getConfigItemId() {
		return $this->configId;
	}

}
