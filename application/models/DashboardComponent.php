<?php
/*****************************************************************************
*       DashboardComponent.php
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


class DashboardComponent extends WebVista_Model_ORM {
    protected $_table = "dashboardComponent";
    protected $_primaryKeys = array("dashboardComponentId");
    protected $content;
    protected $name;
    protected $systemName;
    protected $type;
    protected $dashboardComponentId;

	function __construct() {
		parent::__construct();
	}

    static public function getIterAllComponents() {
       $dashComp = new DashboardComponent();
       $db = Zend_Registry::get('dbAdapter');
       $enumSelect = $db->select()
                        ->from($dashComp->_table)
			->order('dashboardComponent.name ASC');
       $iter = $dashComp->getIterator($enumSelect);

       $iter->rewind(); 
	$resarr = array();
       while ( $iter->valid() ) {
         $item = $iter->current(); $resarr[] = $item; $iter->next();
       }

       return $resarr;
    }

    static public function populateWithGUID($guid) {
        $dob = new DashboardComponent();
        $db = Zend_Registry::get('dbAdapter');
        $guidSelect = $db->select()
                        ->from($dob->_table)
                        ->where("dashboardComponentId = '$guid'");
        $result = $db->query($guidSelect)->fetch();

        return $result; 
    }
}
