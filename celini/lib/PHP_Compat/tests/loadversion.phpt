--TEST--
Method -- PHP_Compat::loadVersion
--FILE--
<?php
require_once ('PHP/Compat.php');

// Singular
$res = PHP_Compat::loadVersion('3.0.0');
echo count($res), "\n";

// Multiple
$comp = array('an-invalid', 'also-invalid', 'more-invalid', 'E_STRICT');
$results = PHP_Compat::loadConstant($comp);

foreach ($results as $comp => $result) {
    echo $comp . ': ';
	echo ($result === false) ? 'false' : 'true', "\n";
}

?>
--EXPECT--
0
an-invalid: false
also-invalid: false
more-invalid: false
E_STRICT: true