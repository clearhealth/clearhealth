<?php
/*****************************************************************************
*       EnumerationsTree.php
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


class EnumerationsTree extends WebVista_Model_ORM {
	
	protected $_tree = array();

	function getTree() {
		return $this->_tree;
	}

	function populate() {
		$db = Zend_Registry::get('dbAdapter');
		$locSelect = $db->select()
                        ->from(array('parent' => 'enumerations'),array())
                        ->join(array('node' => 'enumerations'),'node.lft BETWEEN parent.lft AND parent.rgt',array('parentId','lft','rgt','enumerationId','name'))
			->where('parent.enumerationId = 0')
			->order('node.lft ASC');
		//echo $locSelect->__toString();exit;
		//var_dump($db->query($locSelect)->fetchAll());exit;	
                $this->_tree = $db->query($locSelect)->fetchAll();
	}

	function toXml() {
		$baseStr = '<?xml version="1.0" standalone="yes"?><tree id="0"></tree>';
                $xml = new SimpleXMLElement($baseStr);
                $currentLevel = array($xml);
		$currentRgt = 0;
                foreach($this->_tree as $key => $row) {
			if ($key == 0) {
                        	$currentLevel[0]->addAttribute('lft',$row['lft']);
                        	$currentLevel[0]->addAttribute('rgt',$row['rgt']);
				continue;
			}
			elseif ($row['lft'] > $currentLevel[count($currentLevel)-1]->attributes()->rgt) {
				while ($row['lft'] > $currentLevel[count($currentLevel)-1]->attributes()->rgt) {
					array_pop($currentLevel);
				}
			}
                        $node = $currentLevel[count($currentLevel)-1]->addChild('item');               
                        $node->addAttribute('id',$row['enumerationId']);
                        $node->addAttribute('text',$row['name']);
                        $node->addAttribute('lft',$row['lft']);
                        $node->addAttribute('rgt',$row['rgt']);
			if ($row['rgt'] >  $row['lft']+1) {
                        	$node->addAttribute('child',1);
				array_push($currentLevel,$node);
				$currentRgt = $row['rgt'];
			}
			elseif ($row['rgt']+1 == $currentRgt) { 
				array_pop($currentLevel);
                       		$node->addAttribute('curRgt',$currentLevel[count($currentLevel)-1]->attributes()->rgt);
			}
                }
                return $xml->asXml();
	}

	function addNode($parentId = 0, $newNodeName) {
		$db = Zend_Registry::get('dbAdapter');
		$newNodeId = WebVista_Model_ORM::nextSequenceId();
		$db->beginTransaction();
		try {
    			$sql = "SELECT @lftVal := lft FROM enumerations WHERE enumerationId = " . (int)$parentId;
			$db->query($sql);
			$sql = "UPDATE enumerations SET rgt = rgt + 2 WHERE rgt > @lftVal";
			$db->query($sql);
			$sql = "UPDATE enumerations SET lft = lft + 2 WHERE lft > @lftVal";
			$db->query($sql);
			$sql = "INSERT INTO enumerations(enumerationId, name, parentId, lft, rgt) VALUES($newNodeId," . $db->quote($newNodeName) .", " . (int)$parentId . ",@lftVal + 1, @lftVal + 2)";
			
			$db->query($sql);
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
		}
	}
	function deleteNode($enumerationId) {
		$enumerationId = (int)$enumerationId;
		$db = Zend_Registry::get('dbAdapter');
		$db->beginTransaction();
		try {
			$sql = "SELECT @lftVal := lft, @rgtVal := rgt, @wVal := rgt - lft + 1 FROM enumerations	WHERE enumerationId = $enumerationId";
			$db->query($sql);

			$sql = "DELETE FROM enumerations WHERE lft BETWEEN @lftVal AND @rgtVal";
			$db->query($sql);
			$sql = "UPDATE enumerations SET rgt = rgt - @wVal WHERE rgt > @rgtVal";
			$db->query($sql);
			$sql = "UPDATE enumerations SET lft = lft - @wVal WHERE lft > @rgtVal";
			$db->query($sql);
			
			$db->query($sql);
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
		}
	}
}
