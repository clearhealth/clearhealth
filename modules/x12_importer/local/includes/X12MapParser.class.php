<?php
/**
 * Parser that parses tokenized x12 file using a map defining the elements and there relationships
 * Produces an object tree
 *
 * @package	com.clear-health.x12
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
 * @package	com.clear-health.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 *
 * @todo Refactor into X12TokenParser which takes a {@link X12TokenIterator} and parses it and
 *    add an X12Parser to handle all of the setup/dispatch work that this currently does.
 */
class X12MapParser {
	var $_tokenizer;
	var $_knownElements;
	var $tree = array();

	/**
	 * Debug Levels, debug is a bitmap
	 *
	 * 1 Basic (Num Tokens, Message for each tree line)
	 * 2 Re-Order (full debugging on the reorder phase)
	 * 4 Raw Rows
	 */
	var $debug = 0;

	function loadKnownElementsFromFile($file) {
		include $file;
		$this->_knownElements = $knownElements;
	}

	function loadInput($reader) {
		$this->_tokenizer = new x12Tokenizer();
		$this->_tokenizer->setReader($reader);
	}
	
	function setTreeBuilder($treeBuilder) {
		$this->_treeBuilder = $treeBuilder;
	}

	function getTree() {
		return $this->tree;
	}
	
	function debug($value) {
		echo "$value<br>\n";
	}

	function parse() {
		$tokenizedData = $this->_tokenizer->parse();
		$currentBlock = 0;
		$field = -1;

		if ($this->debug & 1) {
			$this->debug($tokenizedData->count() . " Tokens Parsed");
		}

		$state = X12_MAP_PARSER_STORE_BLOCK_DATA;

		$row = "";
		$start=false;

		for($tokenizedData->rewind(); $tokenizedData->valid(); $tokenizedData->next()) {
			$token = $tokenizedData->current();
			
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
					if (isset($this->_knownElements[$token])) {
						foreach($this->_knownElements[$token] as $key => $val) {
							if ($key != 'fields') {
								$this->tree[$currentBlock]->$key = $val;
							}
						}
					}
					break;
				case X12_MAP_PARSER_STORE_FIELD_DATA:
					if (isset($this->_knownElements[$this->tree[$currentBlock]->code]['fields'])) {
						if (isset($this->_knownElements[$this->tree[$currentBlock]->code]['fields'][$field]['id'])) {
							$id = $this->_knownElements[$this->tree[$currentBlock]->code]['fields'][$field]['id'];
						}
						else {
							$id = $field;
						}
					}
					$this->tree[$currentBlock]->fields[$id] = new x12Field();

					$this->tree[$currentBlock]->fields[$id]->value = $token;
					$this->tree[$currentBlock]->fields[$id]->index = $field;

					if (isset($this->_knownElements[$this->tree[$currentBlock]->code]['fields'][$field])) {
						foreach($this->_knownElements[$this->tree[$currentBlock]->code]['fields'][$field] as $key => $val) {
							$this->tree[$currentBlock]->fields[$id]->$key = $val;
						}
					}
					break;
			}
		}

		$this->_treeBuilder->debug = $this->debug;
		
		$this->_treeBuilder->setRawObjectTree($this->tree);
		$this->tree = $this->_treeBuilder->getBuiltTree();

		if ($this->debug & 2) {
			X12Util::printTree($this->tree);
		}
	}
}
?>
