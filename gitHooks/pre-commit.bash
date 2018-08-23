#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
echo "
===========================================
PHPQA Pre Commit Hook
===========================================
"
readonly projectRoot="$DIR/../../"

cd $projectRoot

# check for stupid
./bin/qa -t lint

# fix some ugly
./bin/qa -t bf

# re add the staged files so that any CS fixes are applied
git add $(git diff --name-only --cached)

echo "
===========================================
PHPQA Pre Commit Hook COMPLETED
===========================================
"
