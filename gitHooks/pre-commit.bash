#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
PHPQA Pre Commit Hook
===========================================
"
readonly projectRoot="$DIR/../../"

cd $projectRoot

./bin/qa -t lint

./bin/qa -t bf

echo "
===========================================
PHPQA Pre Commit Hook COMPLETED
===========================================
"
