<?php
/*****************************************************************************
*       LegacyAppointmentTest.php
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

/**
 * Models_TableModels
 */
require_once 'TableModels.php';

/**
 * LegacyAppointment
 */
require_once 'LegacyAppointment.php';

class Models_LegacyAppointmentTest extends Models_TableModels {

	protected $_keyValues = array('arrived'=>1,
				      'title'=>'Test Title',
				      'reason'=>'Test Reason',);
	protected $_assertMatches = array('title'=>'Test Title');
	protected $_assertTableName = 'appointment'; // value MUST be the same as $_table

}

