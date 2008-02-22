<?php
/**
 * Defines a type that can be used in preferences or config, or any other place where a type needs to be user editable
 *
 * This api is still a work in progress
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class clniTypeCodingTemplate {
	function label($id,$label) {
		//return "<label for='$id'>$label</label>";
		return $label;
	}

	function jsWidget($name) {

		$db =& new clniDb();
		$res = $db->execute("select * from coding_template order by title");

		$ret = "";
		$ret .= "<option value=\"\">No Template</option>\n";
		while($res && !$res->EOF) {
			$ret .= '<option value="'.$res->fields['coding_template_id'].'">'.$res->fields['title']."</option>\n";
			$res->moveNext();
		}
		$ret = '<select name="'.$name.'">' . $ret . '</select>';
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