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
// $Id: array_combine.php,v 1.19 2004/11/14 16:10:50 aidan Exp $


/**
 * Replace array_combine()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_combine
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.19 $
 * @since       PHP 5
 * @require     PHP 4.0.1 (trigger_error)
 */
if (!function_exists('array_combine')) {
    function array_combine($keys, $values)
    {
        if (!is_array($keys)) {
            trigger_error('array_combine() expects parameter 1 to be array, ' . gettype($keys) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_array($values)) {
            trigger_error('array_combine() expects parameter 2 to be array, ' . gettype($values) . ' given', E_USER_WARNING);
            return;
        }

        if (count($keys) !== count($values)) {
            trigger_error('array_combine() Both parameters should have equal number of elements', E_USER_WARNING);
            return false;
        }

        if (count($keys) === 0 || count($values) === 0) {
            trigger_error('array_combine() Both parameters should have number of elements at least 0', E_USER_WARNING);
            return false;
        }

        $keys    = array_values($keys);
        $values  = array_values($values);

        $combined = array ();

        for ($i = 0, $cnt = count($values); $i < $cnt; $i++) {
            $combined[$keys[$i]] = $values[$i];
        }

        return $combined;
    }
}

?>