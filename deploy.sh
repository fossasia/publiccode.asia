#!/bin/bash
# Exit with nonzero exit code if anything fails
set -e

SOURCE_BRANCH="master"
TARGET_BRANCH="gh-pages"

function doCompile {
    ./site/build/build.sh
}

# Pull requests and commits to other branches shouldn't be deployed but just compilled
if [ "$TRAVIS_PULL_REQUEST" != "false" -o "$TRAVIS_BRANCH" != "$SOURCE_BRANCH" ]; then
    echo "Skipping deploy; just doing a build."
    doCompile
    exit 0
fi

# Save some useful information
REPO=`git config remote.origin.url`
SSH_REPO=${REPO/https:\/\/github.com\//git@github.com:}
SHA=`git rev-parse --verify HEAD`

# Clone the existing gh-pages for this repo into out/
# Create a new empty branch if gh-pages doesn't exist yet (should only happen on first deply)
rm -rf site/out
git clone $REPO site/out
cd site/out
git checkout $TARGET_BRANCH || git checkout --orphan $TARGET_BRANCH
cd ..

# Clean out existing contents and move out
rm -rf out/*
cd ..

# Run our compile script
wget -q https://publiccodeasia.firebaseio.com/subscribers/permission.json -O signatures.json
node signatures.js
mv signatures.json site/data/signatures/.
doCompile
cp -r site/public/* site/out
echo publiccode.asia>site/out/CNAME

# Now let's go have some fun with the cloned repo
cd site/out/
git config user.name "Travis CI"
git config user.email "$COMMIT_AUTHOR_EMAIL"

# If there are no changes to the compiled out (e.g. this is a README update) then just bail.
if git diff --quiet; then
    echo "No changes detected. Exiting..."
    exit 0
fi

# Commit the "changes", i.e. the new version.
# The delta will show diffs between new and old versions.
# [skip ci] will skip travis ci build for the commit we are about to make
git add -A .
git commit -m "Deploy to GitHub Pages: ${SHA} [skip ci]"

openssl aes-256-cbc -k $pcbuild -in ../../id_rsa.enc -out ../deploy_key -d
chmod 600 ../deploy_key
eval `ssh-agent -s`
ssh-add ../deploy_key

# Now that we're all set up, we can push.
git push $SSH_REPO $TARGET_BRANCH
