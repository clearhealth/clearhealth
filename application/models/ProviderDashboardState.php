<?php
/*****************************************************************************
*       ProviderDashboardState.php
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


class ProviderDashboardState extends WebVista_Model_ORM {
	protected $providerDashboardStateId;
	protected $_table = "providerDashboardState";
	protected $_primaryKeys = array("providerDashboardStateId");
    protected $personId;
    protected $facilityId;
    protected $global;
    protected $state;
    protected $name;
    protected $layout;

	function __construct() {
        parent::__construct();
    }

    static public function getIterAllTemplates($global = 0, $personId = 0) {
        $enumeration = new ProviderDashboardState();
        $db = Zend_Registry::get('dbAdapter');
        $enumSelect = $db->select()
                        ->from($enumeration->_table)
                        ->where('global = '.(int)$global);
        if ( !$global ) $enumSelect->where('personId = '.(int)$personId);
        $iter = $enumeration->getIterator($enumSelect);

        $iter->rewind(); $resarr = array();
        while ( $iter->valid() ) {
            $item = $iter->current(); $resarr[] = $item; $iter->next();
        }

        return $resarr;
    }

    static public function populateWithProviderDashboardStateId($templateId) {
        $pds = new ProviderDashboardState();
        $db = Zend_Registry::get('dbAdapter');
        $guidSelect = $db->select()
                        ->from($pds->_table)
                        ->where("providerDashboardStateId = ".(int)$templateId);
        $result = $db->query($guidSelect)->fetch();

        return $result; 
    }
}
