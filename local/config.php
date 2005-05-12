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

$config['document_manager']['repository'] = APP_ROOT."/user/documents/";
$config['document_manager']['documents_on_tree'] = true;
$config['document_manager']['category_view'] = false;
$config['document_manager']['type_type'] = false;

$config['template_dir'] = APP_ROOT."/local/templates/";
$GLOBALS['template_dir'] = APP_ROOT."/local/templates/";
$config['openemr_db'] = 'openemr';
$config['autoAcl'] = true;

$config['user_forms_dir'] = APP_ROOT."/user/forms/";

$config['translate']['freeb2'] = "/clearhealth/freeb2/index.php/";

$config['menu']['attachReports'] = true;
$config['menu']['attachForms'] = true;

$config['app_name'] = "clearhealth";

$config['freeb2_wsdl'] = "http://localhost/clearhealth/freeb2/soap_gateway.php?wsdl";
$GLOBALS['C_ALL']['freeb2_dir'] = "/clearhealth/freeb2/index.php/";
$GLOBALS['C_ALL']['emr_dir'] = "/index.php/";

$config['htmldoc'] = "/usr/bin/htmldoc";
$config['use_text_storage'] = true;
?>
