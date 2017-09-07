#!/bin/bash

TRANSLATIONS=

basedir="${0%/*}/.."
cd "$basedir"
mode=$1

# Unite static and language-specific config files to a single file
cat config-static.toml languages/strings.en.toml languages/strings.{de,fr}.toml > config.toml

# Execute hugo buildrun
if [ "$mode" == "server" ]; then
  hugo server
else
  hugo
fi
