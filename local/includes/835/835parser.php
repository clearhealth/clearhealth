<?php

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
	var $tree = array();

	function loadMap($file) {
		include $file;
		$this->_map = $map;
	}

	function loadInput($reader) {
		$this->_tokenizer = new x12Tokenizer();
		$this->_tokenizer->setReader($reader);
	}

	function parse() {
		$this->_tokenizer->parse();
		$currentBlock = -1;
		$field = -1;

		$this->tree[] = new x12Block();
		$currentBlock++;
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
					if ($field == 0) {
						$this->tree[] = new x12Block();
					}
					if (!empty($token)) {
						$this->tree[$currentBlock]->code = $token;
					}
					else {
						echo "Warning empty Token\n";
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

	}
}

$parser = new X12MapParser();
$parser->loadMap('835.map.php');
$parser->loadInput(new FileReader('835.x12'));
$parser->parse();
var_dump($parser->tree);



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
