<?php
################################################################################
## Maintenance Mode
################################################################################

$config['maintenanceMode'] = false;

################################################################################
## Main system settings
################################################################################

//Require https, i.e. force https links even if not detected
$config['forceHTTPS'] = true;

// default starting location /index.php/calendar/day
$config['default_controller'] = "CalendarDisplay";
$config['default_action'] = "day";
$config['CalendarDynamicTimes'] = false;

################################################################################
## Client Side Caching Options
################################################################################
$config['cacheHeadersEnabled'] = true;
// caching notes: using caching will speed up a production site substantially, however it can make development confusing.
$config['cacheHeadersDuration'] = 24 * 3600; // default caching for 24 hours
$config['cacheHeadersImgMaxAge'] = 24 * 3600; // default caching for 24 hours


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
$config['hideCanceledAppointment'] = true;
$config['showRescheduleLink'] = true;
$config['calendar']['showArrivalLink'] = true;
$config['calendar']['showPayLink'] = true;

################################################################################
# Billing Options
################################################################################
$config['billing']['multipleByUnits'] = true;

$config['displayInsuranceElegibility'] = true;
$config['generatePendingClaimsOnly'] = false;

################################################################################
# Labs Module Options
################################################################################
$config['labs'] = array();
//This feature is experimental, do not use.
$config['labs']['highlightAbnormal'] = true;

################################################################################
## Printer options for billing prints offset left X chars
################################################################################
$config['printMargin'] = 3;
//for pageprint

################################################################################
## Patient Picture Options, just create a document category called Picture
################################################################################
$config['PatientPicture'] = array();
$config['PatientPicture']['enabled'] = true;
//width in px, automatically calculates ratioed height
$config['PatientPicture']['thumbWidth'] = 150; 


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
$config['document_manager']['file_command_path'] = '/usr/bin/file';
$config['document_manager']['category_view'] = false;
$config['document_manager']['type_type'] = false;
$config['document_manager']['file_command_path'] = '/usr/bin/file';


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

################################################################################
## Arrival Features
################################################################################
$config['arrival'] = array();
$config['arrival']['patientNoteReason'] = 'Pager Number';
=======

################################################################################
## HealthCloud
################################################################################
$config['healthcloud'] = array();
$config['healthcloud']['servicesUrl'] = "https://openid.clear-health.com/hcapi";

?>
