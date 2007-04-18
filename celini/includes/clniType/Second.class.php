<?php
/**
 * Defines a type that can be used in preferences or config, or any other place where a type needs to be user editable
 *
 * This api is still a work in progress
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class clniTypeSecond {
	function label($id,$label) {
		//return "<label for='$id'>$label</label>";
		return $label;
	}

	function widget($name,$currentValue) {
		$times = array();
		for($i = 5; $i < 65; $i+= 5) {
			$times[$i*60] = $i;
		}
		$ret = "";
		foreach($times as $seconds => $time) {
			$sel = "";
			if ($seconds == $currentValue) {
				$sel = ' selected="selected"';
			}
			$ret .= "<option value='$seconds'$sel>$time</option>\n";
		}
		$ret = '<select id="'.$name.'" name="config[' . $name . ']">' . $ret . '</select> Minutes';
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
