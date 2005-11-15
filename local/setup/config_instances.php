<?php
// This config file will load the database settings based
// on the URL of the site. It should be used when you want
// to run multiple instances of clearhealth using the same
// code base.
//
// Celini Setup, installtion should only require editing of: 
// App Setup, Database Setup, and possible freeb2 integration

// Get the instance name from the URL path
$path_parts = split('/',$_SERVER['REQUEST_URI']);
$instance_name = $path_parts[1];
$instance_config_dir = dirname(__FILE__).'/setup/instances';
$instance_config_file = $instance_config_dir.'/'.$instance_name.'.php';

################################################################################
# App Setup
################################################################################
// Session Name
$config['app_name'] = "clearhealth";

// default starting location /index.php/calendar/day
$config['default_controller'] = "calendar";
$config['default_action'] = "day";

// where forms are uploaded too
$config['user_forms_dir'] = APP_ROOT."/user/forms/";

// where documents are uploaded too
$config['document_manager']['repository'] = APP_ROOT."/user/documents/";

// location of htmldoc utility for creating pds
$config['htmldoc'] = "/usr/bin/htmldoc";

// Do you want an option for group appointments on the calendar
$config['scheduling']['group_appointments'] = false;

################################################################################
# Database Setup
################################################################################
$config['db_type'] = "mysql";
$config['db_host'] = "localhost";
$config['db_user'] = "root";
$config['db_password'] = "";
$config['db_name'] = "clearhealth";
$config['db_table_prefix'] = "";


################################################################################
# freeb2 integration
################################################################################
$GLOBALS['C_ALL']['freeb2_dir'] = "/clearhealth/freeb2/index.php/";
$config['translate']['freeb2'] = "/clearhealth/freeb2/index.php/";


################################################################################
# Celini Options 
################################################################################
// global Celini options
$config['require_login'] = true;
$config['use_text_storage'] = true;
$config['autoAcl'] = true;

// menu config
$config['use_menu'] = true;
$config['menu']['attachReports'] = true;
$config['menu']['attachForms'] = true;
$config['menu']['attachSQL'] = true;

// document management
$config['document_manager']['documents_on_tree'] = true;
$config['document_manager']['category_view'] = false;
$config['document_manager']['type_type'] = false;

// Show extra acl and db debugging info
$config['debug'] = false;

################################################################################
# Other Options
################################################################################

// Openemr compat should be unused
$config['template_dir'] = APP_ROOT."/local/templates/";
$GLOBALS['template_dir'] = APP_ROOT."/local/templates/";
$config['openemr_db'] = 'openemr';

// ???
$GLOBALS['C_ALL']['emr_dir'] = "/index.php/";

// template mappings
$GLOBALS['C_ALL']['group_appointments'] = $config['scheduling']['group_appointments'];

// Load instance specific configuration information.
if(!is_readable($instance_config_file)){
	die("Error loading instance configuration file $instance_config_file");
}
?>
