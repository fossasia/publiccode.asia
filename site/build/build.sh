#!/bin/bash

basedir="${0%/*}/.."
sigdb="$1"

# Clean signatures database
# "$basedir/build/clean_database.py" "$sigdb" "$basedir/data/signatures/data/signatures_clean.json"

# Execute hugo buildrun
cd "$basedir"
hugo
