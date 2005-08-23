#!/bin/bash
find . -maxdepth 2 -name "clearhealth*"  | xargs -i cat {} >> install.sql

