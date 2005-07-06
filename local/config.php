<?php
// Cellini Setup, installtion should only require editing of: 
// App Setup, Database Setup, and possible freeb2 integration

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
$GLOBALS['C_ALL']['freeb2_dir'] = "/freeb2/index.php/";
$config['translate']['freeb2'] = "/freeb2/index.php/";


################################################################################
# Cellini Options 
################################################################################
// global cellini options
$config['require_login'] = true;
$config['use_text_storage'] = true;
$config['autoAcl'] = true;

// menu config
$config['use_menu'] = true;
$config['menu']['attachReports'] = true;
$config['menu']['attachForms'] = true;

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

// Extra CSS file(s) to load
$config['extra_css'] = array('clean.css');

?>
