<?php
/*
 * InstallerSmarty class
 *
 * Base Smarty class to be used by the installer. This
 * class will setup some necessary variables.
 */

require_once realpath(dirname(__FILE__)).'/smarty/Smarty.class.php';

class InstallerSmarty extends Smarty{

	function InstallerSmarty(){
		$this->Smarty();
		$this->template_dir = INSTALLER_PATH;
		$this->cache_dir = $GLOBALS['INSTALLER']['ENGINE']->getSetting('WRITABLE_DIR');
		$this->config_dir = realpath(dirname(__FILE__)).'/smarty';
		$this->compile_dir = $GLOBALS['INSTALLER']['ENGINE']->getSetting('WRITABLE_DIR');
		
		// Setup some variables
		$this->assign('APP_NAME', $GLOBALS['INSTALLER']['ENGINE']->getSetting('APP_NAME'));

	}
}
?>
