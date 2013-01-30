<?php
/*****************************************************************************
*       LegacyAppointment.php
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


class LegacyAppointment extends WebVista_Model_ORM {
    protected $appointment_id;
    protected $arrived;
    protected $title;
    protected $reason;
    protected $walkin;
    protected $created_date;
    protected $last_change_id;
    protected $last_change_date;
    protected $creator_id;
    protected $practice_id;
    protected $provider_id;
    protected $patient_id;
    protected $room_id;
    protected $event_id;
    protected $appointment_code;
    //protected $start;
    //protected $end;
    protected $_table = "appointment";
    protected $_primaryKeys = array("appointment_id");
	protected $_legacyORMNaming = true;

}
