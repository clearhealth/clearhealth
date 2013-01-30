<?php
/*****************************************************************************
*       LocationsTree.php
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


class LocationsTree extends WebVista_Model_ORM {
	
	protected $_tree = array();

	function getTree() {
		return $this->_tree;
	}

	function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$locSelect = $db->select()
                        ->from(array('parent' => 'locations'),array())
                        ->join(array('node' => 'locations'),'node.lft BETWEEN parent.lft AND parent.rgt',array('parentId','locationId','name'))
			->where('parent.locationId = 0')
			->order('node.lft ASC');
		//echo $locSelect->__toString();exit;
		//var_dump($db->query($locSelect)->fetchAll());exit;	
                $this->_tree = $db->query($locSelect)->fetchAll();
	}

	function toXml() {
		$baseStr = '<?xml version="1.0" standalone="yes"?><tree id="0"></tree>';
                $xml = new SimpleXMLElement($baseStr);
                $currentLevel = $xml;
		$currentParent = 0;
		$currentNode = null;
                foreach($this->_tree as $row) {
			if ($currentParent != $row['parentId']) { 
				$currentLevel = $currentNode;
			}
                        $node = $currentLevel->addChild('item');               
                        $node->addAttribute('id',$row['locationId']);
                        $node->addAttribute('text',$row['name']);
			$currentParent = $row['parentId'];
			$currentNode = $node;
                }
                return $xml->asXml();
	}
	function addNode($parentId = 0, $newNodeName) {
		$db = Zend_Registry::get('dbAdapter');
		$newNodeId = WebVista_Model_ORM::nextSequenceId();
		$db->beginTransaction();
		try {
    			$sql = "SELECT @lftVal := lft FROM locations WHERE locationId = " . (int)$parentId;
			$db->query($sql);
			$sql = "UPDATE locations SET rgt = rgt + 2 WHERE rgt > @lftVal";
			$db->query($sql);
			$sql = "UPDATE locations SET lft = lft + 2 WHERE lft > @lftVal";
			$db->query($sql);
			$sql = "INSERT INTO locations(locationId, name, parentId, lft, rgt) VALUES($newNodeId," . $db->quote($newNodeName) .", " . (int)$parentId . ",@lftVal + 1, @lftVal + 2)";
			
			$db->query($sql);
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
		}
	}
}
