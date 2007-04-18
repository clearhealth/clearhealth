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
// $Id: var_export.php,v 1.10 2004/11/14 16:10:50 aidan Exp $


/**
 * Replace var_export()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.var_export
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.10 $
 * @since       PHP 4.2.0
 * @require     PHP 4.0.1 (trigger_error)
 */
if (!function_exists('var_export')) {
    function var_export($array, $return = false)
    {
        // Common output variables
        $indent         = '  ';
        $doublearrow    = ' => ';
        $lineend        = ",\n";
        $stringdelim    = '\'';
        $newline        = "\n";

        // Check the export isn't a simple string / int
        if (is_string($array)) {
            $out = $stringdelim . $array . $stringdelim;
        } elseif (is_int($array)) {
            $out = (string)$array;
        } else {
            // Begin the array export
            // Start the string
            $out = "array (\n";

            // Loop through each value in array
            foreach ($array as $key => $value) {
                // If the key is a string, delimit it
                if (is_string($key)) {
                    $key = $stringdelim . addslashes($key) . $stringdelim;
                }

                // If the value is a string, delimit it
                if (is_string($value)) {
                    $value = $stringdelim . addslashes($value) . $stringdelim;
                } elseif (is_array($value)) {
                    // We have an array, so do some recursion
                    // Do some basic recursion while increasing the indent
                    $recur_array = explode($newline, var_export($value, true));
                    $recur_newarr = array ();
                    foreach ($recur_array as $recur_line) {
                        $recur_newarr[] = $indent . $recur_line;
                    }
                    $recur_array = implode($newline, $recur_newarr);
                    $value = $newline . $recur_array;
                }

                // Piece together the line
                $out .= $indent . $key . $doublearrow . $value . $lineend;
            }

            // End our string
            $out .= ")";
        }


        // Decide method of output
        if ($return === true) {
            return $out;
        } else {
            echo $out;
            return;
        }
    }
}

?>