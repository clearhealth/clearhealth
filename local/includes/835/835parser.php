<?php
error_reporting(E_ALL);

class FileReader {
	var $file;

	function FileReader($file) {
		$this->file = $file;
	}

	function readContents() {
		$contents = trim(file_get_contents($this->file));
		return $contents;
	}
}

class X12Tokenizer {

	var $reader;

	var $tokens;

	function setReader($r) {
		$this->reader = $r;
	}

	function parse() {
		$content = $this->reader->readContents();

		$this->tokens = preg_split('/([*~])[\n\r]*/',$content,0,PREG_SPLIT_DELIM_CAPTURE);
	}

	function rewind() {
		reset($this->tokens);
	}

	function next() {
		next($this->tokens);
	}

	function valid() {
		if (!is_null(key($this->tokens))) {
			return true;
		}
		return false;
	}

	function current() {
		return current($this->tokens);
	}

	function key() {
		return key($this->tokens);
	}
}

define('NEW_BLOCK',0);
define('NEW_FIELD',1);
define('STORE_BLOCK_DATA',2);
define('STORE_FIELD_DATA',3);

class X12MapParser {
	var $_tokenizer;
	var $_map;
	var $_mapTree;
	var $_mapTreeChildren;
	var $tree = array();

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

	function parse() {
		$this->_tokenizer->parse();
		$currentBlock = 0;
		$field = -1;

		$state = STORE_BLOCK_DATA;

		for($this->_tokenizer->rewind(); $this->_tokenizer->valid(); $this->_tokenizer->next()) {
			$token = $this->_tokenizer->current();

			if ($token === '~') {
				$state = NEW_BLOCK;
			}
			else if ($token === '*') {
				$state = NEW_FIELD;
			}

			switch($state) {
				case NEW_BLOCK:
					$currentBlock++;
					$state = STORE_BLOCK_DATA;
					$field = -1;
					break;
				case NEW_FIELD:
					$field++;
					$state = STORE_FIELD_DATA;
					break;
				case STORE_BLOCK_DATA:
					if (!empty($token)) {
						if ($field == 0) {
							$this->tree[$currentBlock] = new x12Block();
						}
						$this->tree[$currentBlock]->code = $token;
					}
					else {
						echo "Warning empty Token<br>\n";
					}
					if (isset($this->_map[$token])) {
						foreach($this->_map[$token] as $key => $val) {
							if ($key != 'fields') {
								$this->tree[$currentBlock]->$key = $val;
							}
						}
					}
					break;
				case STORE_FIELD_DATA:
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
			echo "Unmatched Block Position: $block->code<br>\n";
		}

		$this->tree = $this->_tree;
	}

	function _processMapCode($section,$mapCode,$multi = false) {
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
		if ($childCode == $code) {
			if ($multi !== false) {
				$this->_tree[$section][$multi][$parent][] = $block;
			}
			else {
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

$parser = new X12MapParser();
$parser->loadMap('835.map.php');
$parser->loadInput(new FileReader('835.x12'));
$parser->parse();

treePrinter($parser->tree);
function treePrinter($tree) {
	foreach($tree as $sectionName => $section) {
		echo "<h2 style='margin-bottom:0'>$sectionName</h2>\n<div style='margin-left: 1em'>";
		foreach($section as $blockName => $block) {
			if (is_array($block)) {
				echo "<h3 style='margin:0'>$blockName</h3>\n<div style='margin-left: 1em'>";
				foreach($block as $bName => $b) {
					if(is_array($b)) {
						echo "<h3 style='margin:0'>$bName</h3>\n<div style='margin-left: 1em'>";
						foreach($b as $_bName => $_b) {
							echo "<div>$_b->code - $_b->name (".count($_b->fields).")</div>\n";
						}
						echo "</div>";
					}
					else {
						echo "<div>$b->code - $b->name (".count($b->fields).")</div>\n";
					}
				}
				echo "</div>";
			}
			else {
				echo "<div>$block->code - $block->name (".count($block->fields).")</div>\n";
			}
		}
		echo "</div>";
	}
}



class x12Field {
	var $value;
}

class x12Block {
	var $id;
	var $code;
	var $name;

	var $fields = array();
}
//var_dump($tree);

?>
