<?php
/*****************************************************************************
*       GenericAccessAudit.php
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


class GenericAccessAudit extends WebVista_Model_ORM {

	const CCD_ALL_XML = 5;
	const CCD_VISIT_XML = 6;
	const CCD_ALL_VIEW = 7;
	const CCD_VISIT_VIEW = 8;
	const CCD_ALL_PRINT = 9;
	const CCD_VISIT_PRINT = 10;
	const GROWTH_CHART = 11;

}
