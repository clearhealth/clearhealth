<?php
/*****************************************************************************
*       LabResultTest.php
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
 * LabResult
 */
require_once 'LabResult.php';

class Models_LabResultTest extends Models_TableModels {

	protected $_keyValues = array('lab_test_id'=>1234,
				      'value'=>100,
				      'units'=>'KG',);
	protected $_assertMatches = array('units'=>'KG');
	protected $_assertTableName = 'lab_result'; // value MUST be the same as $_table

}

