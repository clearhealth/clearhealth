#!/bin/bash
# This makes space for imported IDs
php make_id_space.php
# This script targets op-en-hcs and not openemr...
# The following line needs to be replaced with
#php squeeze_openemr_user.php > user_dataset.php
#php squeeze_op-en-hcs_user.php > user_dataset.php
#php inject_user.php
#rm -rf user_dataset.php
# This also needs to import practices/buildings/rooms from openemr
php copy_op-en-hcs_practice.php

