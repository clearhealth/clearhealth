#!/bin/bash

cd `dirname $0`
cat sqltmp/clearhealth*.sql >clearhealth.sql
cat sqltmp/demo*.sql >clearhealth_demodata.sql

