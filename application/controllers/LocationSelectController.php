<?php
/*****************************************************************************
*       LocationSelectController.php
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


class LocationSelectController extends WebVista_Controller_Action
{
    protected $_session;

    public function init()
    {
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }

    public function indexAction()  {
	$db = Zend_Registry::get('dbAdapter');
	$sql = "SELECT case when t4.locationId IS NOT NULL then t4.locationId when t3.locationId IS NOT NULL then t3.locationId when t2.locationId then t2.locationId when t1.locationId IS NOT NULL then t1.locationId END as locationId, concat(ifnull(t1.name,'') ,ifnull(concat('->',t2.name),'') , ifnull(concat('->',t3.name),'') , ifnull(concat('->',t4.name),'') ) as displayName
		FROM locations AS t1
		LEFT JOIN locations AS t2 ON t2.parentId = t1.locationId and t1.locationId != t2.locationId
		LEFT JOIN locations AS t3 ON t3.parentId = t2.locationId and t2.locationId != t3.locationId
		LEFT JOIN locations AS t4 ON t4.parentId = t3.locationId and t3.locationId != t4.locationId
		WHERE t1.locationId = 0
		order by t1.lft";
	$stmt = $db->query($sql);
	$this->view->locations = $stmt->fetchAll();

        $this->render();
    }

}
