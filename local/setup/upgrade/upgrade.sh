#!/bin/bash
OLDDB='CHANGEME'
NEWDB='CHANGEME'
DBUSER='CHANGEME'
DBPASS='CHANGEME'

echo 'Dumping Old Database...'
mysqldump -u$DBUSER -p$DBPASS -c -n -t -Q --insert-ignore --skip-comments --ignore-table=$OLDDB.menu --ignore-table=$OLDDB.ownership $OLDDB > old.sql
# --skip-extended-insert
echo 'Importing Base Install'
mysql -u$DBUSER -p$DBPASS $NEWDB < ../clearhealth-1.0RC3.sql

echo 'Preparing new database'
mysql -u$DBUSER -p$DBPASS $NEWDB < prepare.sql

echo 'Importing old data...'
mysql -u$DBUSER -p$DBPASS --force $NEWDB < old.sql

mysql -u$DBUSER -p$DBPASS $NEWDB < postupgrade.sql

echo 'Importing appointments'
php -q upgrade_appointments.php

echo 'Upgrading schedules'
php -q upgrade_schedules.php

echo 'Importing billing data'
php -q upgrade_billing.php

echo 'Upgrading users'
php -q upgrade_users.php

echo 'Upgrade Complete!!!'
