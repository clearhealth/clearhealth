#!/bin/bash

cd `dirname $0`
cat sqltmp/clearhealth*.sql >clearhealth-$1.sql

