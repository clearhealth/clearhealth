<?php
/**
* Create an array from a block of text, returns nothing but the array is 
* assigned as a normal smarty variable
*
* Format is
*
* {list name="blah"}
* value=text
* value=text
* {/list}
*/
function smarty_block_list($params, $content, &$smarty, &$repeat)
{
	$delim = "=";
    if (isset($content)) {
        $name = $params['name'];
	if (isseT($params['delimiter'])) {
		$delim = $params['delimiter'];
	}

	$array = array();
	$tmp = explode("\n",$content);
	foreach($tmp as $line) {
		if (preg_match('/(.+)'.$delim.'(.+)/',trim($line),$match)) {
			$array[$match[1]] = $match[2];
		}
	}
	$smarty->_tpl_vars[$name] = $array;
    }
}
?>
