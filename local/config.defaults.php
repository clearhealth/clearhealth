<?php
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


################################################################################
# Celini Options 
################################################################################
// global Celini options
$config['require_login'] = true;
$config['use_text_storage'] = true;
$config['autoAcl'] = true;
?>
