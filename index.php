<?php
/*****************************************************************************
*       index.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/

/*
class Me {
	var $_id;
	var $_objects;
}
class User {
	var $user_id;
        var $person_id;
        var $username;
}
*/
session_name('clearhealth');
function calcTS() {
        list($usec, $sec) = explode(" ", microtime());
        $ts = ((float)$usec + (float)$sec);
        if (!isset($GLOBALS['gts'])) $GLOBALS['gts'] = $ts;
        return $ts-$GLOBALS['gts'];
}
if (!function_exists('lcfirst')) {
	function lcfirst($str)  {
		$str[0] = strtolower($str[0]);
		return (string)$str;
	}
}
function __($key) {
	//$translate = Zend_Registry::get('translate');

	//$rtext = $translate->_($key);
	//if ($key == $rtext) {
	//	trigger_error("untranslated: '$key' => '',", E_USER_NOTICE);
	//}
	//if (strlen($rtext) == 0) {
		return $key;
	//}
	//return $rtext;

}
calcTS();
define ('APPLICATION_ENVIRONMENT','production');
require_once './application/library/WebVista/App.php';
WebVista::getInstance()->run();

