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
// | Authors: Philippe Jausions <Philippe.Jausions@11abacus.com>          |
// |          Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: version_compare.php,v 1.10 2004/11/21 14:22:44 aidan Exp $


/**
 * Replace version_compare()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.version_compare
 * @author      Philippe Jausions <Philippe.Jausions@11abacus.com>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.10 $
 * @since       PHP 4.1.0
 * @require     PHP 4.0.1 (trigger_error)
 */
if (!function_exists('version_compare')) {
    function version_compare($version1, $version2, $operator = '<')
    {
        // Check input
        if (!is_scalar($version1)) {
            trigger_error('version_compare() expects parameter 1 to be string, ' . gettype($version1) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_scalar($version2)) {
            trigger_error('version_compare() expects parameter 2 to be string, ' . gettype($version2) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_scalar($operator)) {
            trigger_error('version_compare() expects parameter 3 to be string, ' . gettype($operator) . ' given', E_USER_WARNING);
            return;
        }

        // Standardise versions
        $v1 = explode('.',
            str_replace('..', '.',
                preg_replace('/([^0-9\.]+)/', '.$1.',
                    str_replace(array('-', '_', '+'), '.',
                        trim($version1)))));

        $v2 = explode('.',
            str_replace('..', '.',
                preg_replace('/([^0-9\.]+)/', '.$1.',
                    str_replace(array('-', '_', '+'), '.',
                        trim($version2)))));

        // Replace empty entries at the start of the array
        while (empty($v1[0]) && array_shift($v1)) {}
        while (empty($v2[0]) && array_shift($v2)) {}

        // Describe our release states
        $versions = array(
            'dev'   => 0,
            'alpha' => 1,
            'a'     => 1,
            'beta'  => 2,
            'b'     => 2,
            'RC'    => 3,
            'p'     => 4,
            'pl'    => 4);

        // Loop through each segment in the version string
        $compare = 0;
        for ($i = 0, $x = min(count($v1), count($v2)); $i < $x; $i++) {
            if ($v1[$i] == $v2[$i]) {
                continue;
            }
            if (is_numeric($v1[$i]) && is_numeric($v2[$i])) {
                $compare = ($v1[$i] < $v2[$i]) ? -1 : 1;
            } elseif (is_numeric($v1[$i])) {
                $compare = 1;
            } elseif (is_numeric($v2[$i])) {
                $compare = -1;
            } elseif (isset($versions[$v1[$i]]) && isset($versions[$v2[$i]])) {
                $compare = ($versions[$v1[$i]] < $versions[$v2[$i]]) ? -1 : 1;
            } else {
                $compare = strcmp($v2[$i], $v1[$i]);
            }

            break;
        }

        // If previous loop didn't find anything, compare the "extra" segments
        if ($compare == 0) {
            if (count($v2) > count($v1)) {
                if (isset($versions[$v2[$i]])) {
                    $compare = ($versions[$v2[$i]] < 4) ? 1 : -1;
                } else {
                    $compare = -1;
                }
            } elseif (count($v2) < count($v1)) {
                if (isset($versions[$v1[$i]])) {
                    $compare = ($versions[$v1[$i]] < 4) ? -1 : 1;
                } else {
                    $compare = 1;
                }
            }
        }

        // Compare the versions
        if (func_num_args() > 2) {
            switch ($operator) {
                case '>':
                case 'gt':
                    return (bool) ($compare > 0);
                    break;
                case '>=':
                case 'ge':
                    return (bool) ($compare >= 0);
                    break;
                case '<=':
                case 'le':
                    return (bool) ($compare <= 0);
                    break;
                case '==':
                case '=':
                case 'eq':
                    return (bool) ($compare == 0);
                    break;
                case '<>':
                case '!=':
                case 'ne':
                    return (bool) ($compare != 0);
                    break;
                case '':
                case '<':
                case 'lt':
                    return (bool) ($compare < 0);
                    break;
                default:
                    return;
            }
        }

        return $compare;
    }
}

?>