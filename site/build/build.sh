#!/bin/bash

# Put all available languages here, except "en". Separated by spaces
TRANSLATIONS="de fr it nl"

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
elif [ "$mode" == "syntax" ]; then
  hugo
else
  hugo

  #
  # After building the website, we set the AWS credentials and uploda
  # everything to our AWS s3 bucket.
  #
  . /srv/cred/aws.sh
  /usr/local/bin/aws s3 cp /usr/share/blog/public/ s3://aws-website-pmpc-soegm/ --recursive
fi
