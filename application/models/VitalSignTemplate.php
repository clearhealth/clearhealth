<?php
/*****************************************************************************
*       VitalSignTemplate.php
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


class VitalSignTemplate extends WebVista_Model_ORM {
	protected $vitalSignTemplateId;
	protected $template;
	protected $_primaryKeys = array('vitalSignTemplateId');
	protected $_table = "vitalSignTemplates";
	
	public static function generateVitalSignsTemplateKeyValue($vitalSignTemplateId = 1) {
		$vitalSignTemplate = new self();
		$vitalSignTemplate->vitalSignTemplateId = $vitalSignTemplateId;
		$vitalSignTemplate->populate();
		$vitals = array();
		try {
			$template = new SimpleXMLElement($vitalSignTemplate->template);
			foreach ($template as $vital) {
				$title = (string)$vital->attributes()->title;
				$vitals[$title] = (string)$vital->attributes()->label;
			}
		}
		catch (Exception $e) {
			WebVista::debug($e->getMessage());
		}
		return $vitals;
	}

}
