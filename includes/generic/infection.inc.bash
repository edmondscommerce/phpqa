#!/usr/bin/env bash
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
set -x

# We need to be able to generate coverage for infection to calculate its metrics
phpCmd=\php
${phpCmd} ./bin/infection \
--threads=${numberOfCores} \
--configuration=${infectionConfig} \
--min-msi=${mutationScoreIndicator} \
--min-covered-msi=${coveredCodeMSI}
set +x

