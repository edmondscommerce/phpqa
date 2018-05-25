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
$(hostname) $0 $@
===========================================
"
if [[ "1" == "${phpunitCoverage}" ]]
then
    wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover $TRAVIS_BUILD_DIR/var/qa/phpunit_logs/coverage.clover
fi

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
