<?php
$config['debug'] = false;
$config['db_type'] = "mysql";
$config['db_host'] = "localhost";
$config['db_user'] = "root";
$config['db_password'] = "";
$config['db_name'] = "clearhealth";
$config['db_table_prefix'] = "";
$config['require_login'] = true;

$config['default_controller'] = "calendar";
$config['default_action'] = "day";
$config['use_menu'] = true;

$config['documents']['repository'] = APP_ROOT."/user/documents/";

$config['use_storage'] = false;

$config['template_dir'] = APP_ROOT."/local/templates/";
$GLOBALS['template_dir'] = APP_ROOT."/local/templates/";
$config['openemr_db'] = 'openemr';
$config['autoAcl'] = false;

$config['user_forms_dir'] = APP_ROOT."/user/forms/";
?>
