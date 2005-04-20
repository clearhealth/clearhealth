#!/bin/bash
php squeeze_openemr_patientdata.php > patient_dataset.php
php inject_patientdata.php
rm -rf patient_dataset.php
php squeeze_op-en-hcs_calendar.php > calendar_dataset.php
php inject_calendar.php
rm -rf calendar_dataset.php
