<?php
/**
 * Parser that parses tokenized x12 file using a map defining the elements and there relationships
 * Produces an object tree
 *
 * @package	com.uversainc.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */



/**#@+
 * Contstants for parser states
 */
define('X12_MAP_PARSER_NEW_BLOCK',0);
define('X12_MAP_PARSER_NEW_FIELD',1);
define('X12_MAP_PARSER_STORE_BLOCK_DATA',2);
define('X12_MAP_PARSER_STORE_FIELD_DATA',3);
/**#@+*/

/**
 * Parser that parses tokenized x12 file using a map defining the elements and there relationships
 * Produces an object tree
 *
 * @package	com.uversainc.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class X12MapParser {
	var $_tokenizer;
	var $_map;
	var $_mapTree;
	var $_mapTreeChildren;
	var $tree = array();

	/**
	 * Debug Levels, debug is a bitmap
	 *
	 * 1 Basic (Num Tokens, Message for each tree line)
	 * 2 Re-Order (full debugging on the reorder phase)
	 * 4 Raw Rows
	 */
	var $debug = 0;

	function loadMap($file) {
		include $file;
		$this->_map = $map;
		$this->_mapTree = $tree;
		$this->_mapTreeChildren = $children;
	}

	function loadInput($reader) {
		$this->_tokenizer = new x12Tokenizer();
		$this->_tokenizer->setReader($reader);
	}

	function getTree() {
		return $this->tree;
	}
	
	function debug($value) {
		echo "$value<br>\n";
	}

	function parse() {
		$this->_tokenizer->parse();
		$currentBlock = 0;
		$field = -1;

		if ($this->debug & 1) {
			$this->debug(count($this->_tokenizer->tokens)." Tokens Parsed");
		}

		$state = X12_MAP_PARSER_STORE_BLOCK_DATA;

		$row = "";
		for($this->_tokenizer->rewind(); $this->_tokenizer->valid(); $this->_tokenizer->next()) {
			$token = $this->_tokenizer->current();
			if ($this->debug & 4) {
				$row .= $token;
			}

			if ($token === '~') {
				$state = X12_MAP_PARSER_NEW_BLOCK;
				if ($this->debug & 4) {
					$this->debug($row);
					$row = '';
				}
			}
			else if ($token === '*') {
				$state = X12_MAP_PARSER_NEW_FIELD;
			}

			switch($state) {
				case X12_MAP_PARSER_NEW_BLOCK:
					$currentBlock++;
					$state = X12_MAP_PARSER_STORE_BLOCK_DATA;
					$field = -1;
					break;
				case X12_MAP_PARSER_NEW_FIELD:
					$field++;
					$state = X12_MAP_PARSER_STORE_FIELD_DATA;
					break;
				case X12_MAP_PARSER_STORE_BLOCK_DATA:
					if (!empty($token)) {
						if ($field == 0) {
							$this->tree[$currentBlock] = new x12Block();
						}
						$this->tree[$currentBlock]->code = $token;
						if ($this->debug & 1) {
							$this->debug("$token");
						}
					}
					else {
						trigger_error("Warning empty Token");
					}
					if (isset($this->_map[$token])) {
						foreach($this->_map[$token] as $key => $val) {
							if ($key != 'fields') {
								$this->tree[$currentBlock]->$key = $val;
							}
						}
					}
					break;
				case X12_MAP_PARSER_STORE_FIELD_DATA:
					if (isset($this->_map[$this->tree[$currentBlock]->code]['fields'])) {
						if (isset($this->_map[$this->tree[$currentBlock]->code]['fields'][$field]['id'])) {
							$id = $this->_map[$this->tree[$currentBlock]->code]['fields'][$field]['id'];
						}
						else {
							$id = $field;
						}
					}
					$this->tree[$currentBlock]->fields[$id] = new x12Field();

					$this->tree[$currentBlock]->fields[$id]->value = $token;
					$this->tree[$currentBlock]->fields[$id]->index = $field;

					if (isset($this->_map[$this->tree[$currentBlock]->code]['fields'][$field])) {
						foreach($this->_map[$this->tree[$currentBlock]->code]['fields'][$field] as $key => $val) {
							$this->tree[$currentBlock]->fields[$id]->$key = $val;
						}
					}
					break;
			}
		}

		$this->_reorder();

	}

	/**
	 * Apply the map tree putting blocks into loops
	 */
	function _reorder() {
		$this->_tree = array();

		foreach($this->_mapTree as $section => $sectionMap) {
			foreach($sectionMap as $mapCode) {
				if (is_array($mapCode)) {
					$cardinality = array_shift($mapCode);
					switch($cardinality) {
						case '+':
							$loop = true;
							$l = 0;
							while($loop) {
								$loop = false;
								foreach($mapCode as $c) {
									$tmp = $this->_processMapCode($section,$c,$l);
									if ($tmp) {
										$loop = true;
									}
								}
								$l++;
							}
							break;
					}
				}
				else {
					$this->_processMapCode($section,$mapCode);
				}
			}
		}

		foreach($this->tree as $block) {
			trigger_error("Unmatched Block Position: $block->code");
		}

		$this->tree = $this->_tree;
	}

	function _processMapCode($section,$mapCode,$multi = false) {
		if ($this->debug & 2) {
			$this->debug("##$section:$mapCode:$multi");
		}
		if (isset($this->_mapTreeChildren[$mapCode])) {
			$ret = false;
			foreach($this->_mapTreeChildren[$mapCode] as $childCode) {
				if (substr($childCode,-1) == '+') {
					$childCode = substr($childCode,0,-1);
					while($this->_processChildBlock($childCode,$section,$mapCode,$multi)) { $ret = true;}
				}
				else {
					$r = $this->_processChildBlock($childCode,$section,$mapCode,$multi);
					if ($r) {
						$ret = true;
					}
				}
			}
			return $ret;
		}
		else {
			$block = array_shift($this->tree);
			$code = $block->code;
			if ($mapCode == $code) {
				if ($this->debug & 2) {
					$this->debug("[$section][] = $block->code");
				}
				$this->_tree[$section][] = $block;
				return true;
			}
			else {
				array_unshift($this->tree,$block);
			}
		}
		return false;
	}

	function _processChildBlock($childCode,$section,$parent,$multi) {
		$block = array_shift($this->tree);
		$code = $block->code;
		if ($this->debug & 2) {
			$this->debug("$childCode == $code");
		}
		if ($childCode == $code) {
			if ($multi !== false) {
				if ($this->debug & 2) {
					$this->debug("[$section][$multi][$parent][] = $block->code");
				}
				$this->_tree[$section][$multi][$parent][] = $block;
			}
			else {
				$this->debug("[$section][$parent][] = $block->code");
				$this->_tree[$section][$parent][] = $block;
			}
			return true;
		}
		else {
			array_unshift($this->tree,$block);
			return false;
		}
	}
}
?>
