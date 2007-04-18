<?php
require_once realpath(dirname(__FILE__)).'/Installer.php';

$smarty = new InstallerSmarty();

$GLOBALS['INSTALLER']['SMARTY'] =& $smarty;
$output = '';
if (!isset($run_output)) {
	$run_output = '';
}
$run_output .= $GLOBALS['INSTALLER']['ENGINE']->run();
$smarty->assign('INSTALLER_PHASE', $GLOBALS['INSTALLER']['ENGINE']->getPhaseName());

$output .= $smarty->fetch(Installer::getTemplatePath('header.tpl'));
$output .= $run_output;
$output .= $smarty->fetch(Installer::getTemplatePath('footer.tpl'));

print($output);
?>
