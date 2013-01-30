<?php
/*****************************************************************************
*       XMLParserAbstract.php
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


abstract class XMLParserAbstract extends WebVista_Model_ORM {

	protected $_depth = 0;
	protected $_xp = null;

	abstract public function characterData($parser,$data);

	public function startElement($parser,$name,array $attribs) {
		$this->_depth++;
	}

	public function endElement($parser,$name) {
		$this->_depth--;
	}

	protected function _initParser() {
		$this->_xp = xml_parser_create('UTF-8');
		xml_set_object($this->_xp,$this);
		xml_set_element_handler($this->_xp,'startElement','endElement');
		xml_set_character_data_handler($this->_xp,'characterData');
		xml_parser_set_option($this->_xp,XML_OPTION_SKIP_WHITE,1);
		xml_parser_set_option($this->_xp,XML_OPTION_CASE_FOLDING,0);
	}

	public function __construct() {
		$this->_initParser();
	}

	public function __destruct() {
		if ($this->_xp === null) return;
		xml_parser_free($this->_xp);
	}

	private function __clone() {}

	public function parse($filename) {
		if (!($fp = fopen($filename,'r'))) {
			trigger_error('Error: could not open XML input.');
			return -1;
		}

		set_time_limit(0);
		while ($data = fread($fp, 4096)) {
			if (!xml_parse($this->_xp,$data,feof($fp))) {
				trigger_error(sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($this->_xp)),xml_get_current_line_number($this->_xp)));
				return -1;
			}
		}
	}

	public function parseString($data) {
		if (!xml_parse($this->_xp,$data)) {
			trigger_error(sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($this->_xp)),xml_get_current_line_number($this->_xp)));
			return -1;
		}
	}

}
