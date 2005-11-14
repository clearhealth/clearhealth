<?php
/**
 * Defines a type that can be used in preferences or config, or any other place where a type needs to be user editable
 *
 * This api is still a work in progress
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class clniTypeFacilityType {
	function label($id,$label) {
		//return "<label for='$id'>$label</label>";
		return $label;
	}

	function widget($name,$currentValue) {
		$types=array(
			0=>'General',
			1=>'Dentistry'
		);
		$ret = "";
		foreach($types as $key=>$type) {
			$sel = "";
			if ($key == $currentValue) {
				$sel = " SELECTED";
			}
			$ret .= "<option value='$key'$sel>$type</option>\n";
		}
		$ret = '<select id="'.$key.'" name="config[' . $name . ']">' . $ret . '</select>';
		return $ret;
	}

	/**
	 * Parses out the value from the widget returning it in the needed type
	 */
	function parseValue($input) {
		return (int)$input;
	}
}
?>