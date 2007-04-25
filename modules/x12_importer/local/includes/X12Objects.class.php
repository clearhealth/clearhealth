<?php
/**
 * Base objects used in generated object tree
 *
 * @package	com.uversainc.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

class X12Field {
	var $value;
	var $type = 'string';
	
	function value() {
		switch ($this->type) {
			case 'array' :
				return explode(':', $this->value);
				break;
				
			case 'string' :
			default :
				return $this->value;
				break;
		}
	}
}

class X12Block {
	var $id;
	var $code;
	var $name;

	var $fields = array();
}
?>
