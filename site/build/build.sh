#!/bin/bash

# Put all available languages here, except "en". Separated by spaces
TRANSLATIONS="ca de el eo es fr hu it nl sv tr zh_tw"

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
  status=1
  tries=0
  while [[ $status -ne 0 ]]; do

    hugo
    status=$?
    
    (( tries++ ))
    
    if [[ $status != 0 && $tries -le 2 ]]; then
      echo "Build error with exit status $status on try $tries. Try again now"
    elif [[ $status != 0 && $tries -gt 2 ]]; then
      echo "Build failed 3 times in a row. Don't try again."
      exit 1
    fi
  
  done
    
  ## After successfully building the website, we set the AWS credentials and uplodad
  ## everything to our AWS s3 bucket.
  ##
  #if [ -f /srv/cred/aws.sh ]; then
  #  . /srv/cred/aws.sh
  #  /usr/local/bin/aws configure set default.s3.max_concurrent_requests 2
  #  /usr/local/bin/aws s3 cp /usr/share/blog/public/ s3://aws-website-pmpc-soegm/ --recursive
  #fi
fi
