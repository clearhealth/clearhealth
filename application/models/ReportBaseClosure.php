<?php
/*****************************************************************************
*       ReportBaseClosure.php
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


class ReportBaseClosure extends ClosureBase {

	protected $_table = 'reportBaseClosures';
	protected $_ormClass = 'ReportBase';

	protected static function _generateTree(SimpleXMLElement $xml,$reportBaseId) {
		static $reportBaseList = array();
		$reportBaseClosure = new self();
		$descendants = $reportBaseClosure->getClosureTreeById($reportBaseId);
		$item = null;
		foreach ($descendants as $row) {
			if (in_array($row->reportBaseId,$reportBaseList)) {
				continue;
			}
			if ($item === null) {
				$item = $xml;
			}
			$leaf = $item->addChild('row');
			$leaf->addAttribute('id',$row->reportBaseId);
			$leaf->addChild('cell',$row->displayName);
			$leaf->addChild('cell',$row->systemName);
			$reportBaseList[] = $row->reportBaseId;
			if ($reportBaseId != $row->reportBaseId) { // prevents infinite loop
				self::_generateTree($leaf,$row->reportBaseId);
			}
		}
	}

	public static function generateXMLTree(SimpleXMLElement $xml) {
		$reportBaseClosure = new self();
		$reportBaseClosureIterator = $reportBaseClosure->getAllTopLevelRoots();

		foreach ($reportBaseClosureIterator as $reportBase) {
			$item = $xml->addChild('row');
			$item->addAttribute('id',$reportBase->reportBaseId);
			$item->addChild('cell',$reportBase->displayName);
			$item->addChild('cell',$reportBase->systemName);
			self::_generateTree($item,$reportBase->reportBaseId);
		}
		return $xml;
	}

}
