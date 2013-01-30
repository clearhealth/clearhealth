<?php
/*****************************************************************************
*       TemplateXSLT.php
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


class TemplateXSLT {

	public static function toXml($data,$rootNodeName = 'data',$xml = null) {
		if (is_null($xml)) {
			$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><'.$rootNodeName.' />');
		}

		// loop through the data passed in.
		foreach($data as $key => $value) {
			// no numeric keys in our xml please!
			if (is_numeric($key)) {
				$key = $rootNodeName;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_0-9]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value)) {
				// create a new node unless this is an array of elements
				$node = self::isAssoc($value) ? $xml->addChild($key) : $xml;

				// recrusive call - pass $key as the new rootNodeName
				self::toXml($value, $key, $node);
			}
			else {
				// add single node.
				$value = htmlentities($value);
				$xml->addChild($key,$value);
			}
		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}

	// determine if a variable is an associative array
	public static function isAssoc($array) {
		return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}

	public static function render(Array $data,$templateXSLT) {
		$strXml = self::toXml($data,$rootNodeName = 'data',$xml = null);
		$docXml = new DOMDocument();
		if (!$docXml->loadXML($strXml)) {
			throw new Exception('Generated XML is invalid');
		}
		$xslt = new XSLTProcessor();
		$docXsl = new DOMDocument();
		if (!$docXsl->loadXML($templateXSLT)) {
			throw new Exception('Template XSLT is invalid');
		}
		$xslt->importStylesheet($docXsl);
		return $xslt->transformToXML($docXml);
	}

}
