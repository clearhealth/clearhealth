<?php
/**
 * Util functions that can be used for debugging etc
 *
 * @package	com.uversainc.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

class X12Util {

	function printTree($tree) {
		foreach($tree as $sectionName => $section) {
			echo "<h2 style='margin-bottom:0'>$sectionName</h2>\n<div style='margin-left: 1em'>";
			foreach($section as $blockName => $block) {
				if (is_array($block)) {
					echo "<h3 style='margin:0'>$blockName</h3>\n<div style='margin-left: 1em'>";
					foreach($block as $bName => $b) {
						if(is_array($b)) {
							echo "<h3 style='margin:0'>$bName</h3>\n<div style='margin-left: 1em'>";
							foreach($b as $_bName => $_b) {
								echo "<div>$_b->code - $_b->name (".count($_b->fields).")</div>\n";
							}
							echo "</div>";
						}
						else {
							echo "<div>$b->code - $b->name (".count($b->fields).")</div>\n";
						}
					}
					echo "</div>";
				}
				else {
					echo "<div>$block->code - $block->name (".count($block->fields).")</div>\n";
				}
			}
			echo "</div>";
		}
	}
}
?>
