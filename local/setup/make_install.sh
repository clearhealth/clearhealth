#!/bin/bash
find . -maxdepth 2 -name "clearhealth*.sql"  | xargs -i cat {} >> install.sql

