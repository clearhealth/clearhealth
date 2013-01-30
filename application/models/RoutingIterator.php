<?php
/*****************************************************************************
*       RoutingIterator.php
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


class RoutingIterator extends WebVista_Model_ORMIterator implements Iterator {

    public function __construct($dbSelect = null) {
        parent::__construct("Routing",$dbSelect);
    }

	public function setFilter($filter) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select(null)
			->from('routing','*')
			->join('person','person.person_id = routing.personId',null)
			->join('patient','patient.person_id = routing.personId',null)
			//->join(array('ids' => new Zend_Db_Expr('(select max(routingId) as routingId from routing group by personId)')),'routing.routingId = ids.routingId',null)
			->columns(array("concat(person.last_name,', ' , person.first_name, ' ' , substring(person.middle_name,0,1)) as patient",
				'patient.record_number',
				'routing.stationId',
				'routing.fromStationId',
				'TIMESTAMPDIFF(MINUTE ,routing.timestamp, NOW()) AS minutes'
			))
			//->where("routing.timestamp >= DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00')")
			//->where("routing.timestamp <= DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')")
			//->group("person.person_id")
			->order("routing.timestamp DESC")
			->order("routing.checkInTimestamp DESC");
			if (isset($filter['stationId']) && strlen($filter['stationId']) > 0) {
				$dbSelect->where("routing.stationId = ?", $filter['stationId']);
			}
			elseif (isset($filter['personId']) && $filter['personId'] > 0) {
			$dbSelect->where("person.person_id = ?", (int)$filter['personId']);
			}
			else {
				$dbSelect->where("false");
			}
		//echo $dbSelect->__toString();exit;
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
