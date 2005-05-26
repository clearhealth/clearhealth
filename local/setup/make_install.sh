#!/bin/bash
find . -maxdepth 1 -name "clearhealth*"  | xargs -i cat {} >> install.sql

