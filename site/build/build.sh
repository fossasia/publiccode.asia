#!/bin/bash

# Put all available languages here, except "en". Separated by spaces
TRANSLATIONS="de fr nl"

basedir="${0%/*}/.."
cd "$basedir"
mode=$1

# Unite static and language-specific config files to a single file
for language in $TRANSLATIONS; do
  languagefiles="$languagefiles languages/strings.$language.toml"
done
cat config-static.toml languages/strings.en.toml ${languagefiles} > config.toml

# Execute hugo buildrun
if [ "$mode" == "server" ]; then
  hugo server
else
  hugo
fi
