#!/bin/bash
svn checkout --username ftrotter --password 4fred  https://svn2.uversainc.com/svn/clearhealth/clearhealth/trunk/ clearhealth
svn checkout --username ftrotter --password 4fred  https://svn2.uversainc.com/svn/cellini/trunk/ clearhealth/cellini
svn checkout --username ftrotter --password 4fred  https://svn2.uversainc.com/svn/freeb2/trunk/ freeb2
svn checkout --username ftrotter --password 4fred  https://svn2.uversainc.com/svn/cellini/trunk/ freeb2/cellini
pwd
revnum=`/usr/bin/svnversion clearhealth | cut -f '2' -d ':'`
echo "<?php echo ' \"$revnum\" <br>'; ?>" > clearhealth/revision.php
find -name ".svn" | xargs -i rm -rf {}
find -name "nopkg*" | xargs -i rm -rf {}
find ./clearhealth/local/setup/ -name "clearhealth*"  | xargs -i cat {} >> ./clearhealth/local/setup/install.sql
tar -czvf clearhealth.$revnum.tgz
