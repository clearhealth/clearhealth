<?php
/*****************************************************************************
*       Routing.php
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

class Routing extends WebVista_Model_ORM {

	var $routingId		= '';
	var $personId		= '';
	var $stationId		= '';
	var $appointmentId	= '';
	var $providerId		= '';
	var $roomId		= '';
	var $fromStationId	= '';
	var $timestamp		= '';
	var $checkInTimestamp	= '';

	protected $_table = "routing";
        protected $_primaryKeys = array("routingId");

	const ENUM_PARENT_NAME = 'Routing Preferences';

	public function populateByAppointments() {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('personId = ?',(int)$this->personId)
			       ->where('appointmentId = ?',(int)$this->appointmentId)
			       ->where('providerId = ?',(int)$this->providerId)
			       ->where('roomId = ?',(int)$this->roomId);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}
	
}
?>
