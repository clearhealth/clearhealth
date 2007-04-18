<?php
/**
* Add css to the header of the document
* Head is also a literal tag so you don't have to worry about using {literal} tags
* unless you need use template variables inside it.
* Example:
* {head type="css"}.className { margin-left: 5px; }{/head}
*/
function smarty_block_head($params, $content, &$smarty, &$repeat)
{
	$type = $params['type'];

	$head =& celini::HTMLHeadInstance();

	switch(strtolower($type)) {

		case 'css':
			$head->addInlineCss($content);
			break;
		case 'js':
			$head->addInlineJs($content);
			break;
		case 'externaljs':
			$head->addJs($content);
			break;
		case 'externalcss':
			$head->addExternalCss($content);
			break;
		default:
			$head->addElement($content);
	}
}
?>