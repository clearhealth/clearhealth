#!/bin/bash
rm -f install.sql
find ./sqltmp -maxdepth 1 -name "clearhealth*.sql"  | xargs -i cat {} >> install.sql

