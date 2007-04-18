<?php
/**
 * Only display a block of content once
 */
function smarty_block_displayOnce($params, $content, &$smarty, &$repeat) {
	$contentHash = md5($content);
	if (isset($GLOBALS['_clni']['SmartyDisplayOnce'][$contentHash])) {
		return '';
	}
	
	$GLOBALS['_clni']['SmartyDisplayOnce'][$contentHash] = true;
	return $content;
}

?>
