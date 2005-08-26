<?php
/*
 * Created on Aug 16, 2005
 *
 * Example version file for application installer
 */
$base_app_path = realpath(dirname(__FILE__).'/../');

$versions = new VersionSet();

$version_1rc2 = new Version('1.0RC2');
$version_1rc2->collectData('db_user', 'Database Username', 'text', 'root');
$version_1rc2->collectData('db_password', 'Database Password', 'text', '');
$version_1rc2->collectData('db_server', 'Database Server', 'text', 'localhost');
$version_1rc2->collectData('db_database', 'Database Name', 'text', '');
$version_1rc2->addTest('PHPVersion', array('4.3.0'));
$version_1rc2->addTest('PHPExtension', array('mysql'));
$version_1rc2->addTest('WritableLocation', array($base_app_path.'/tmp'));
$version_1rc2->addTest('MysqlVersion', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'version' => '4.1.0'));
$version_1rc2->addAction('AcceptText', array(dirname(__FILE__).'/LICENSE'));
$version_1rc2->addAction('SQLFile', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'db_field' => 'db_database',
'files' => array($base_app_path.'/local/setup/clearhealth-0.1RC2.sql', $base_app_path.'/freeb/local/setup/freeb2.sql')));
$version_1rc2->addAction('ReplaceString', array(
'message' => "Saved database configuration information!",
'files' => array($base_app_path.'/local/config.php.dist' => $base_app_path.'/local/config.php'),
'fields' => array('INSTALL_DB_USERNAME' => 'db_user', 'INSTALL_DB_PASSWORD' => 'db_password', 'INSTALL_DB_DATABASE' => 'db_database', 'INSTALL_DB_SERVER' => 'db_server')
));

$versions->add($version_1rc2);
?>
