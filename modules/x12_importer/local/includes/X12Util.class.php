<?php
/**
 * Util functions that can be used for debugging etc
 *
 * @package	com.uversainc.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

class X12Util {

	function printTree($tree) {
		foreach($tree as $name => $subtree) {
			echo "<h1 style='margin-bottom:0'>$name</h1>\n";
			echo "<hr>";
			foreach($subtree as $sectionName => $section) {
				echo "<h2 style='margin-bottom:0'>$sectionName</h2>\n<div style='margin-left: 1em'>";
				X12Util::printBlock($sectionName,$section);
				echo "</div>";
			}
		}
	}

	function printBlock($blockName,$block){
		if (is_array($block)) {
			if (!is_int($blockName)) {
				echo "<h3 style='margin:0'>$blockName</h3>\n<div style='margin-left: 1em'>";
			}
			foreach($block as $bName => $b) {
				X12Util::printBlock($bName,$b);
			}
			if (!is_int($blockName)) {
				echo "</div>";
			}
		} else {
			echo "<div>$block->code - $block->name (".count($block->fields).")</div>\n";
			//echo "<pre>".print_r($block->fields,true)."</pre>\n";
		}
	}
}
?>
