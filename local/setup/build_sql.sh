#!/bin/bash

cd `dirname $0`
cat sqltmp/clearhealth*.sql >clearhealth-$1.sql
cat sqltmp/codes*.sql >clearhealth_codedata-$1.sql
cat sqltmp/demo*.sql >clearhealth_demodata-$1.sql

