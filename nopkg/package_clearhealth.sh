#!/bin/bash
svn checkout --username username --password password  https://svn2.uversainc.com/svn/clearhealth/clearhealth/trunk/ clearhealth
svn checkout --username username --password password  https://svn2.uversainc.com/svn/cellini/trunk/ clearhealth/cellini
svn checkout --username username --password password  https://svn2.uversainc.com/svn/freeb2/trunk/ freeb2
svn checkout --username username --password password  https://svn2.uversainc.com/svn/cellini/trunk/ freeb2/cellini
pwd
revnum=`/usr/bin/svnversion clearhealth | cut -f '2' -d ':'`
echo "<?php echo \"$revnum\"; ?>" > clearhealth/revision.php
#find -name ".svn" | xargs -i rm -rf {}
#find -name "nopkg*" | xargs -i rm -rf {}
/bin/rm -rf ./clearhealth/local/setup/install.sql
find ./clearhealth/local/setup/ -maxdepth 1 -name "clearhealth*"  | tee sqlfilelist | xargs -i cat {} >> ./clearhealth/local/setup/install.sql
tar -czvf clearhealth.$revnum.tgz clearhealth/ freeb2/
