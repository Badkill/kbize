#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag ${TAG}
box build -v

#
# Copy executable file into GH pages
#
git checkout gh-pages

cp bin/kbize.phar downloads/kbize-${TAG}.phar
git add downloads/kbize-${TAG}.phar

SHA1=$(openssl sha1 bin/kbize.phar | cut -d ' ' -f2)

JSON='name:"kbize.phar"'
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"http://silvadanilo.github.io/kbize/downloads/kbize-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

if [ -f kbize.phar.pubkey ]; then
    cp kbize.phar.pubkey pubkeys/kbize-${TAG}.phar.pubkeys
    git add pubkeys/kbize-${TAG}.phar.pubkeys
    JSON="${JSON},publicKey:\"http://silvadanilo.github.io/kbize/pubkeys/kbize-${TAG}.phar.pubkey\""
fi

#
# Update manifest
#
cat manifest.json | jq ".[] + {${JSON}} | [.]" > manifest.json.tmp
mv manifest.json manifest.json.back
mv manifest.json.tmp manifest.json
git add manifest.json

git commit -m "Bump version ${TAG}"

#
# Go back to master
#
git checkout master

echo "\n\n"
echo "New version created. Now you should run:"
echo "git push origin gh-pages"
echo "git push ${TAG}"
