#!/bin/bash

cd `dirname $0`
cat sqltmp/*.sql >clearhealth-$1.sql

