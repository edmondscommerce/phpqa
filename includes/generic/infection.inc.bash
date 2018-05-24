#!/usr/bin/env bash
infectionExitCode=99
while (( infectionExitCode > 0 ))
do
    set +e
    onlyCovered=( -- )
    if [[ "1" == "$infectionOnlyCovered" && "0" == "${phpunitFailedOnlyFiltered:-0}" ]]
    then
        onlyCovered+=( --only-covered )
    fi
    set -x
    phpNoXdebug -f ./bin/infection \
        ${onlyCovered[@]} \
        --threads=${infectionThreads} \
        --configuration=${infectionConfig} \
        --min-msi=${infectionMutationScoreIndicator} \
        --min-covered-msi=${infectionCoveredCodeMSI} \
        --coverage=$varDir/phpunit_logs \
        --test-framework-options=" --no-coverage -c ${phpUnitConfigPath} "

    infectionExitCode=$?
    set -e
    set +x
    if (( $infectionExitCode > 0 ))
    then
        tryAgainOrAbort "Infection"
    fi
done
