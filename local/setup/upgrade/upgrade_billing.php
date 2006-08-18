<?php
/**
 * An importer to handle upgrading all old (pre 1.0RC3) payment claimlines to the new (1.0RC3) 
 *
 * @access private
 */

// comment out if you want to run via the web
if (isset($_SERVER['HTTP_HOST'])) {
	die('Unauthorized access prohibited');
}

// setup php environment to use a lot of ram and run forever
set_time_limit(0);
ini_set('memory_limit','1024M');

// hide error message about starting sessions after output is generated
session_start();

include('config.php');


debug("\nInitializing Celini...", false);
// initial Celini environment
define('APP_ROOT', realpath(dirname(__FILE__) . '/../../../'));
define('CELINI_ROOT', APP_ROOT . '/celini');
define('CELLINI_ROOT', CELINI_ROOT);
require_once CELINI_ROOT . '/bootstrap.php';

$db = new clniDB();
debug("done!");
$oldCHDB = $GLOBALS['oldCHDB'];
debug("Querying Payments...",false);
$sql = "
	SELECT
		pc.*,cd.coding_data_id
	FROM
		{$oldCHDB}.payment p
		LEFT JOIN {$oldCHDB}.payment_claimline pc USING(payment_id)
		LEFT JOIN {$oldCHDB}.clearhealth_claim cc ON(p.foreign_id=cc.claim_id)
		LEFT JOIN {$oldCHDB}.coding_data cd ON(pc.code_id=cd.code_id AND (cd.foreign_id=p.encounter_id OR cd.foreign_id=cc.encounter_id))
	WHERE pc.payment_claimline_id IS NOT NULL
	GROUP BY
		pc.payment_claimline_id";
//debug($sql);
$oldres = $db->execute($sql);

debug("done!");
debug("Found " . $oldres->recordCount() . " old payment claimlines\n");
debug("Converting into new format.");

$sqls = array();
$fields = array(
'payment_claimline_id',
'payment_id',
'code_id',
'paid',
'writeoff',
'carry',
'coding_data_id'
);

while($oldres && !$oldres->EOF) {
	$sqlz = array();
	foreach($fields as $field) {
		$sqlz[] = $db->quote($oldres->fields[$field]);
	}
	$sqls[] = "(".implode(',',$sqlz).")";
	if(count($sqls) > 50) {
		addNew($sqls);
	}
	$oldres->MoveNext();
}
if(count($sqls) > 0) {
	addNew($sqls);
}
function addNew(&$sqls) {
	global $db;
	$sql = "REPLACE INTO {$GLOBALS['newCHDB']}.payment_claimline (payment_claimline_id,payment_id,code_id,paid,writeoff,carry,coding_data_id) VALUES ".implode(',',$sqls);
	$db->execute($sql);
	$sqls = array();
}

debug("done.");



/**
 * Outputs debugging code if debugging is turned on.
 *
 * @param string
 * @access private
 */
function debug($string, $lineEnd = true) {
	if ($GLOBALS['debug']) {
		echo $string;
		if ($lineEnd) {
			echo $GLOBALS['eol-style'];
		}
	}
}

?>
