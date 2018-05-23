#!/usr/bin/env bash
infectionExitCode=99
while (( infectionExitCode > 0 ))
do
    set +e
    set -x
    onlyCovered=()
    if [[ "1" == "$infectionOnlyCovered" ]]
    then
        onlyCovered+=( --only-covered )
    fi
    phpNoXdebug ./bin/infection \
        ${onlyCovered[@]} \
        --threads=${infectionThreads} \
        --configuration=${infectionConfig} \
        --min-msi=${infectionMutationScoreIndicator} \
        --min-covered-msi=${infectionCoveredCodeMSI} \
        --coverage=$varDir/phpunit_logs \
        --test-framework-options=" -c ${phpUnitConfigPath} "

    infectionExitCode=$?
    set -e
    set +x
    if (( $infectionExitCode > 0 ))
    then
        tryAgainOrAbort "Infection"
    fi
done
