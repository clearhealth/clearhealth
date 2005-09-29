#!/bin/bash
rm -f install.sql
find . -maxdepth 2 -name "clearhealth*.sql"  | xargs -i cat {} >> install.sql

