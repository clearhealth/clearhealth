<?php
$loader->requireOnce('includes/X12TreeBuilder.abstract.php');

/**
 * Builds a tree based off of Josh's map/tree array combo.
 */
class X12MapTreeBuilder extends X12TreeBuilder
{
	var $_mapTree;
	var $_mapTreeChildren;
	/**
	 * Debug Levels, debug is a bitmap
	 *
	 * 1 Basic (Num Tokens, Message for each tree line)
	 * 2 Re-Order (full debugging on the reorder phase)
	 * 4 Raw Rows
	 */
	var $debug = 0;
	
	/**
	 * Load a map tree and its children from a given file
	 *
	 * @param string $file
	 */
	function loadMapFromFile($file) {
		include $file;
		$this->setMapTree($tree);
		$this->setMapTreeChildren($children);
	}
	
	/**
	 * Set the map tree
	 *
	 * @param array $tree
	 */
	function setMapTree($tree) {
		$this->_mapTree = $tree;
	}
	
	/**
	 * Sets the map tree children
	 *
	 * @param array $children
	 */
	function setMapTreeChildren($children) {
		$this->_mapTreeChildren = $children;
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function buildTree() {
		/*
		foreach($this->_rawObjectTree as $o) {
			echo "$o->code<br>\n";
		}
		*/
		reset($this->_rawObjectTree);

		$this->_builtTree = $this->_parseGroup('', $this->_mapTree);
	}
	
	function _parseGroup($name, $group) {
		$this->debug($name);
		$returnArray = array();
		
		if (substr($name, -1) == '+') {
			$continue = true;
			$key = substr($name, 0, -1);
			$returnArray[$key] = array();
			
			while ($continue) {
				$tmp = $this->_parseGroup($key, $group);
				if (count($tmp) == 0) {
					$continue = false;
					break;
				}
				$returnArray[$key][] = $tmp;
			}
		}
		else if (substr($name, -1) == '*') {
			$key = substr($name, 0, -1);
			$returnArray[$key] = array();

			$count = count($group);
			
			while ($count > 0) {
				foreach($group as $subName => $sub) {
					$this->debug("<b>$subName</b>");
					$tmp = $this->_parseGroupWorker($sub, array());
					if (count($tmp) == 0) {
						$count--;
						break;
					}
					else {
						$count = count($group);
					}
					$returnArray[$key][][$subName] = $tmp;
				}
			}
		}
		else {
			$returnArray =  $this->_parseGroupWorker($group,$returnArray);
		}
		
		$this->debug("end $name");
		return $returnArray;
	}

	function _parseGroupWorker($group,$returnArray) {
		foreach ($group as $blockKey => $blockName) {
			if (is_array($blockName)) {
				$realKey = $blockKey;
				if (substr($blockKey, -1) == '+' || substr($blockKey, -1) == '*') {
					$realKey = substr($blockKey, 0, -1);
				}
				$tmp = $this->_parseGroup($blockKey, $blockName);
				if (count($tmp) > 0) {
					if (substr($blockKey, -1) == '+') {
						$tmp = $tmp[$realKey];
					}
					if (substr($blockKey, -1) == '*') {
						$tmp = $tmp[$realKey];
					}
					if (count($tmp) > 0) {
						$returnArray[$realKey] = $tmp;
					}
				}
			}
			else {
				$currentBlock = current($this->_rawObjectTree);
				if ($currentBlock->code == $blockName) {
					$this->debug(" -- $currentBlock->code");
					$id = $currentBlock->id;
					$c = 1;
					while(isset($returnArray[$id])) {
						$id = $currentBlock->id.$c;
						$c++;
					}
					$returnArray[$id] = $currentBlock;
					next($this->_rawObjectTree);
				}
			}
		}
		return $returnArray;
	}

	function debug($value) {
		if ($this->debug & 8) {
			echo "$value<br>\n";
		}
	}
}

?>
