<?php
/*
 * Created on Aug 16, 2005
 *
 * Example version file for application installer
 */
$base_app_path = realpath(dirname(__FILE__).'/../');

/*
 * Our search and replaced is based on the / character, so we must 
 * get an address that is free of them... By doing this we avoid // 
 * in the search string. This will probably break if there is a / in the string
 *
 * Eventually the replace string should move to be / insensitive.
 */
$webpath = substr($_SERVER['PHP_SELF'],1,strpos(strtolower($_SERVER['PHP_SELF']),"/installer/index.php")-1);
if ($webpath == "") $webpath = "/";
$versions = new VersionSet();
$versions->collectData('db_user', 'Database Username', 'text', 'root');
$versions->collectData('db_password', 'Database Password', 'text', '');
$versions->collectData('db_server', 'Database Server', 'text', 'localhost');
$versions->collectData('db_database', 'Database Name', 'text', 'clearhealth');
$versions->collectData('db_port', 'Database Server Port', 'text', '3306');
$versions->collectData('mysql_path', 'Path to Mysql (no trailing slash)', 'text', '/usr/bin');
$versions->collectData('admin_password', 'Set new password for the admin account', 'text', 'admin');

$version_1rc3 = new Version('2.3');
$version_1rc3->addTest('PHPVersionOver', array('5.1.6'));
$version_1rc3->addTest('PHPMemory', array('64M'));
$version_1rc3->addTest('PHPMagicQuotes', array('Off'));
$version_1rc3->addTest('PHPRegisterGlobals', array('Off'));
$version_1rc3->addTest('PHPExtension', array('mysql'));
$version_1rc3->addTest('WritableLocation', array($base_app_path.'/tmp'));
$version_1rc3->addTest('WritableLocation', array($base_app_path.'/tmp/cache'));
$version_1rc3->addTest('WritableLocation', array($base_app_path.'/local/config.php'));
$version_1rc3->addTest('MysqlVersionOver', array(
	'username_field' => 'db_user',
	'password_field' => 'db_password',
	'server_field' => 'db_server',
	'port_field' => 'db_port',
	'version' => '5.0.0'));
$version_1rc3->addAction('AcceptText', array(dirname(__FILE__).'/LICENSE'));
$version_1rc3->addAction('SQLFile', array(
	'username_field' => 'db_user',
	'password_field' => 'db_password',
	'server_field' => 'db_server',
	'port_field' => 'db_port',
	'db_field' => 'db_database',
	'mysql_path' => 'mysql_path',
	'cache_files' => array(
		$base_app_path.'/local/setup/ch_2_3_install.sql'	
	)));

$version_1rc3->addAction('ReplaceString', array(
	'message' => "Saved database configuration information!",
	'files' => array(
		$base_app_path.'/local/config.php.dist' => $base_app_path.'/local/config.php'),
	'fields' => array(
		'INSTALL_DB_USERNAME' => 'db_user',
		'INSTALL_DB_PASSWORD' => 'db_password', 
		'INSTALL_DB_DATABASE' => 'db_database', 
		'INSTALL_DB_SERVER' => 'db_server'),

	'strings' => array ('INSTALL_BASE_DIR' => $webpath)
	));


$version_1rc3->addAction('SQLOptions', array(
	'username_field' => 'db_user',
	'password_field' => 'db_password',
	'server_field' => 'db_server',
	'port_field' => 'db_port',
	'db_field' => 'db_database',
	'mysql_path' => 'mysql_path',
	'files' => array(
		$base_app_path.'/local/setup/code_packs' 
	)));

$version_1rc3->addAction('SetAdminPassword', array(
	'username_field' => 'db_user',
	'password_field' => 'db_password',
	'server_field' => 'db_server',
	'port_field' => 'db_port',
	'admin_password' => 'admin_password',
	'database_name' => 'db_database',
	));



$versions->add($version_1rc3);
?>
