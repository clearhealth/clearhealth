<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: md5_file.php,v 1.1 2004/11/14 18:21:17 aidan Exp $


/**
 * Replace md5_file()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/md5_file
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.1 $
 * @since       PHP 4.2.0
 * @require     PHP 4.0.1 (trigger_error)
 * @internal    raw_output not implemented
 */
if (!function_exists('md5_file')) {
    function md5_file($filename, $raw_output = false)
    {
        // Sanity check
        if (!is_scalar($filename)) {
            trigger_error('md5_file() expects parameter 1 to be string, ' . gettype($filename) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_scalar($raw_output)) {
            trigger_error('md5_file() expects parameter 2 to be bool, ' . gettype($raw_output) . ' given', E_USER_WARNING);
            return;
        }

        if (!file_exists($filename)) {
            trigger_error('md5_file() Unable to open file', E_USER_WARNING);
            return false;
        }

        $file = file_get_contents($filename);
        return md5($file);
    }
}

?>