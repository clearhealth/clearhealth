<?php
/*****************************************************************************
*       AuditValueTest.php
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
 * AuditValue
 */
require_once 'AuditValue.php';

class Models_AuditValueTest extends Models_TableModels {

	protected $_keyValues = array('_ormPersist'=>true,
				      'auditId'=>1234,
				      'key'=>'Test Key',
				      'value'=>'Test Value',
				      'line1'=>'ClearHealth St.',);
	protected $_assertMatches = array('auditId'=>1234);
	protected $_assertTableName = 'auditValues'; // value MUST be the same as $_table

}

