<?php
/**
 * Defines a type that can be used in preferences or config, 
 * or any other place where a type needs to be user editable
 *
 * This api is still a work in progress
 *
 * @package	com.uversainc.celini
 * @author	Gordon Forsythe <gforsythe@uversainc.com>
 */
class clniTypeTime {
	function label($id,$label) {
		//return "<label for='$id'>$label</label>";
		return $label;
	}

	function widget($name,$currentValue = '00:00:00') {
		$hourstotal = 12;
		$minutestotal = 60;
		$secondstotal = 60;
		$ampm = 'AM';
		list($hours,$minutes,$seconds) = explode(':',$currentValue);
		if($hours > 12){
			$hours -= 12;
			$ampm = 'PM';
		}
		
		$out = "<select id='$name' name='config[$name][hours]'>\n";
		$h = 0;
		while($h <= $hourstotal) {
			$sel='';
			if($h == (int)$hours) {
				$sel = ' selected';
			}
			$out.= "<option value='$h'$sel>$h</option>\n";
			$h++;
		}
		$out.="</select>\n";

		$out .= "<select id='$name' name='config[$name][minutes]'>\n";
		$h = 0;
		while($h <= $minutestotal) {
			$sel='';
			if($h == (int)$minutes) {
				$sel = ' selected';
			}
			$out.= "<option value='$h'$sel>$h</option>\n";
			$h++;
		}
		$out.="</select>\n";

		$out .= "<select id='$name' name='config[$name][seconds]'>\n";
		$h = 0;
		while($h <= $minutestotal) {
			$sel='';
			if($h == (int)$seconds) {
				$sel = ' selected';
			}
			$out.= "<option value='$h'$sel>$h</option>\n";
			$h++;
		}
		$out.="</select>\n";

		$out .= "<select id='$name' name='config[$name][ampm]'>\n";
		$out .= "<option value='AM'";
		if($ampm == 'AM') $out .=' selected';
		$out .= ">AM</option><option value='PM'";
		if($ampm == 'PM') $out .= ' selected';
		$out .= ">PM</option>\n</select>";

		return $out;
	}

	/**
	 * Parses out the value from the widget returning it in the needed type
	 */
	function parseValue($input) {
		$hours = $input['hours'];
		$minutes = $input['minutes'];
		$seconds = $input['seconds'];
		$ampm = $input['ampm'];
		$time = strtotime("$hours:$minutes:$seconds $ampm");
		$time = date('H:i:s',$time);
		return $time;
	}
}
?>