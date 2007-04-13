<?php
//Require https, i.e. force https links even if not detected
$config['forceHTTPS'] = "false";

// default starting location /index.php/calendar/day
$config['default_controller'] = "CalendarDisplay";
$config['default_action'] = "day";

################################################################################
# Confidential Actions
################################################################################
// Actions to display a warning on when in we have a selected user 
// and confidentaility level is set to 5
$config['confidentialActions'] = array();
$config['confidentialActions']['patient']['*'] = true; 
$config['confidentialActions']['patientdashboard']['*'] = true; 
$config['confidentialActions']['encounter']['*'] = true; 

################################################################################
# Modules
################################################################################
$config['module_paths']['billing'] = APP_ROOT . '/modules/billing';
$config['module_paths']['labs']    = APP_ROOT . '/modules/labs';
$config['module_paths']['x12import'] = APP_ROOT . '/modules/x12_importer';
$config['module_paths']['calendar'] = APP_ROOT . '/modules/calendar'; 
$config['module_paths']['referral'] = APP_ROOT . '/modules/referral'; 
$config['module_paths']['docsmart'] = APP_ROOT . '/modules/docsmart'; 
$config['module_paths']['pharmacy'] = APP_ROOT . '/modules/pharmacy'; 
 
################################################################################ 
# Calendar Module Options  
################################################################################ 
$config['calendar']['event_render'] = 'CalendarEventRender'; 
$config['calendar']['ajax_handler'] = 'CalendarAJAXHandler'; 
$config['calendar']['data_handler'] = 'ClearhealthCalendarData';

$config['showCalendarWeekViewLinks'] = true;
$config['hideCanceledAppointment'] = false;
$config['showRescheduleLink'] = false;

################################################################################
# Billing Options
################################################################################
$config['billing']['multipleByUnits'] = false;

$config['displayInsuranceElegibility'] = false;
$config['generatePendingClaimsOnly'] = false;

################################################################################
## Printer options
################################################################################
$config['printMargin'] = 3;

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
$config['menu']['attachArrays'] = true;

// document management
$config['document_manager']['documents_on_tree'] = true;
$config['document_manager']['category_view'] = false;
$config['document_manager']['type_type'] = false;

// Show extra acl and db debugging info
$config['debug'] = false;

// enable change auditing
$config['auditChanges'] = true;
$config['auditFieldChanges'] = true;

// use the new style ownership table
$config['ownership'] = false;
$config['ordo_registry'] = true;

################################################################################
## Locale settings
################################################################################
// Default date/time formats
$config['locale']['date_format']      = "%m/%d/%Y";
$config['locale']['time_format']      = "%H:%i";
$config['locale']['timestamp_format'] = sprintf("%s %s",
	$config['locale']['date_format'],
	$config['locale']['time_format']);
?>
