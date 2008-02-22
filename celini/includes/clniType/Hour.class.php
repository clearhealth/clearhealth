<?php
/**
 * Defines a type that can be used in preferences or config, 
 * or any other place where a type needs to be user editable
 *
 * This api is still a work in progress
 *
 * @package	com.clear-health.celini
 * @author	Gordon Forsythe <gforsythe@uversainc.com>
 */
class clniTypeHour {
	function label($id,$label) {
		//return "<label for='$id'>$label</label>";
		return $label;
	}

	function widget($name,$currentValue = '') {
		$hourstotal = 24;
		
		$out = "<select id='$name' name='config[$name]'>\n";
		$h = 1;
		while($h <= $hourstotal) {
			$sel='';
			if($h == (int)$currentValue) {
				$sel = ' selected';
			}
			$out.= "<option value='$h'$sel>$h</option>\n";
			$h++;
		}
		$out.="</select> Hour(s)\n";

		return $out;
	}

	/**
	 * Parses out the value from the widget returning it in the needed type
	 */
	function parseValue($input) {
		return (int)$input;
	}
}
?>