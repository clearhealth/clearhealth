#!/bin/bash
# This script targets op-en-hcs and not openemr...
# The following line needs to be replaced with
# php squeeze_openemr_user.php > user_dataset.php
php squeeze_op-en-hcs_user.php > user_dataset.php
php inject_user.php
rm -rf user_dataset.php
