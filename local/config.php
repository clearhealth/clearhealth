<?php
$config['debug'] = false;
$config['db_type'] = "mysql";
$config['db_host'] = "localhost";
$config['db_user'] = "root";
$config['db_password'] = "root";
$config['db_name'] = "clearhealth";
$config['db_table_prefix'] = "";
$config['require_login'] = true;

$config['default_controller'] = "calendar";
$config['default_action'] = "day";
$config['use_menu'] = true;

$config['document_manager']['repository'] = APP_ROOT."/user/documents/";
$config['document_manager']['documents_on_tree'] = true;
$config['document_manager']['category_view'] = false;
$config['document_manager']['type_type'] = false;

$config['template_dir'] = APP_ROOT."/local/templates/";
$GLOBALS['template_dir'] = APP_ROOT."/local/templates/";
$config['openemr_db'] = 'openemr';
$config['autoAcl'] = false;

$config['user_forms_dir'] = APP_ROOT."/user/forms/";

$config['translate']['freeb2'] = "/freeb2/index.php/";
?>
