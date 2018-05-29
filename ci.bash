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
export phpqaQuickTests=0
export phpUnitQuickTests=0
export phpUnitCoverage=${phpunitCoverage:-0}
export CI=true

# run the QA pipeline, echo tee to stdout and also to log file
bin/qa |& tee var/qa/ci.log

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
