<?php
// $Id: function.formatPhoneNumber.php 1019 2006-02-09 21:08:43Z cpowers $
/**
 * Smarty plugin
 * @package com.clearhealth
 * @subpackage smarty_plugins
 */


/**
 * Calculates BMI from provided weight and height
 *
 * @param array
 * @param {@link Smarty}
 * @return string
 */
function smarty_function_calcBMI($params, &$smarty) {

	foreach($params as $_key => $_val) {
        switch($_key) {
            case 'weight':
                $$_key = (int)$_val;
                break;
            case 'inches':
                $$_key = (int)$_val;
                break;
            case 'feet':
                $$_key = (int)$_val;
                break;
            case 'lbs':
                $$_key = (int)$_val;
                break;
            case 'meters':
                $$_key = (int)$_val;
                break;
            case 'kilos':
                $$_key = (int)$_val;
                break;
	}
	}
	$bmi = 'NaN';
	if ($meters > 0 && $kilos > 0) {
        	$calcedBmi = round($kilos/($meters*$meters),2);
        	$bmi = $calcedBmi;
	}
	elseif($inches > 0 || $feet > 0 && $lbs > 0) {
		$totalInches = ($feet*12)+$inches;
        	$calcedBmi = round(($lbs*702) /($totalInches*$totalInches),1);
        	$bmi = $calcedBmi;
	}
	return $bmi ;
}

?>
