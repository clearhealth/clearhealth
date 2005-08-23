#!/bin/bash
php squeeze_openemr.php > dataset.php
php inject_openemr.php
rm -rf dataset.php
