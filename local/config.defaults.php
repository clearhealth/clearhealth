<?php
################################################################################
# Confidential Actions
################################################################################
// Actions to display a warning on when in we have a selected user 
// and confidentaility level is set to 5
$config['confidentialActions'] = array();
$config['confidentialActions']['patient']['*'] = true; 
$config['confidentialActions']['patientDashboard']['*'] = true; 
$config['confidentialActions']['encounter']['*'] = true; 
?>
