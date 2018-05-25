#!/usr/bin/env bash

function runInfection(){
#    local extraArgs=( -- )
#    if [[ "1" == "$infectionOnlyCovered" && "0" ]]
#    then
#        extraArgs+=( --only-covered )
#    fi
    if [[ "0" == "${phpunitFailedOnlyFiltered:-0}" ]]
    then
        phpNoXdebug ./bin/infection \
            --coverage=$varDir/phpunit_logs \
            --threads=${infectionThreads} \
            --configuration=${infectionConfig} \
            --min-msi=${infectionMutationScoreIndicator} \
            --min-covered-msi=${infectionCoveredCodeMSI} \
            --test-framework-options=" --no-coverage -c ${phpUnitConfigPath}"
    else
        phpNoXdebug ./bin/infection \
            --threads=${infectionThreads} \
            --configuration=${infectionConfig} \
            --min-msi=${infectionMutationScoreIndicator} \
            --min-covered-msi=${infectionCoveredCodeMSI} \
            --test-framework-options=" -c ${phpUnitConfigPath}"
    fi
}

infectionExitCode=99
while (( infectionExitCode > 0 ))
do
    backupIFS=$IFS
    IFS=$standardIFS
    set +e
    set -x
    rm -rf $varDir/infection/*
    runInfection
    infectionExitCode=$?
    set -e
    set +x
    IFS=$backupIFS
    if (( $infectionExitCode > 0 ))
    then
        tryAgainOrAbort "Infection"
    fi
done

