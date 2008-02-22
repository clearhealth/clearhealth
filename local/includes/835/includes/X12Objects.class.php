<?php
/**
 * Base objects used in generated object tree
 *
 * @package	com.clear-health.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

class X12Field {
	var $value;
}

class X12Block {
	var $id;
	var $code;
	var $name;

	var $fields = array();
}
?>
