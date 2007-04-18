<?php
/*
 * Created on Aug 16, 2005
 *
 * SureInvoice version file for application installer
 */
 
$versions = new VersionSet();
$versions->collectData('db_user', 'Database Username', 'text', 'root');
$versions->collectData('db_password', 'Database Password', 'text', '');
$versions->collectData('db_server', 'Database Server', 'text', 'localhost');
$versions->collectData('db_port', 'Database Port', 'text', '3306');
$versions->collectData('db_database', 'Database Name', 'text', '');

// 0.2 Release
$version_0_2 = new Version('0.2');
$version_0_2->addAction('SQLFile', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'db_field' => 'db_database',
'files' => array(realpath(dirname(__FILE__).'/sql').'/sureinvoice.sql')));

$versions->add($version_0_2);

// 0.3 Release
$version_0_3 = new Version('0.3');
$version_0_3->addAction('SQLFile', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'db_field' => 'db_database',
'files' => array(realpath(dirname(__FILE__).'/sql').'/update-0.2-0.3.sql')));
$versions->add($version_0_3);

// 0.4 Release
$version_0_4 = new Version('0.4');
$version_0_4->addAction('SQLFile', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'db_field' => 'db_database',
'files' => array(realpath(dirname(__FILE__).'/sql').'/update-0.3-0.4.sql')));
$versions->add($version_0_4);

// 1.0 Release
$version_1_0 = new Version('1.0');
$version_1_0->addTest('PHPVersionOver', array('4.3.0'));
$version_1_0->addTest('PHPExtension', array('mysql', 'curl'));
$version_1_0->addTest('WritableLocation', array(realpath(dirname(__FILE__).'/../').'/includes/global_config.php', realpath(dirname(__FILE__).'/tmp')));
$version_1_0->addTest('MysqlVersionOver', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'version' => '4.1.0'));
$version_1_0->addAction('AcceptText', array(dirname(__FILE__).'/LICENSE'));
$version_1_0->addAction('SQLFile', array(
'username_field' => 'db_user',
'password_field' => 'db_password',
'server_field' => 'db_server',
'port_field' => 'db_port',
'db_field' => 'db_database',
'files' => array(realpath(dirname(__FILE__).'/sql').'/update-0.3-0.4.sql')));
$version_1_0->addAction('ReplaceString', array(
'message' => "Saved database configuration information!",
'files' => array(dirname(__FILE__).'/test_replacement.txt'),
'fields' => array('INSTALL_DB_USERNAME' => 'db_user', 'INSTALL_DB_PASSWORD' => 'db_password'),
'strings' => array('TEST_STRING' => 'This is my test string text!')
));
$versions->add($version_1_0);
?>
