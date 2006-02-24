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

$config['module_paths'] = array();

################################################################################
# Celini Options 
################################################################################
// global Celini options
$config['require_login'] = true;
$config['use_text_storage'] = true;
$config['autoAcl'] = true;
?>
