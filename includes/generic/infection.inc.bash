#!/usr/bin/env bash

function runInfection(){
    local extraArgs=( -- )
    if [[ "1" == "$infectionOnlyCovered" && "0" ]]
    then
        extraArgs+=( --only-covered )
    fi
    if [[ "0" == "${phpunitFailedOnlyFiltered:-0}" ]]
    then
        # Don't run infection with xdebug
         phpNoXdebug -f ./bin/infection \
            "${extraArgs[@]}" \
            --coverage=$varDir/phpunit_logs \
            --threads=${infectionThreads} \
            --configuration=${infectionConfig} \
            --min-msi=${infectionMutationScoreIndicator} \
            --min-covered-msi=${infectionCoveredCodeMSI} \
            --log-verbosity=all
    else
        ${phpBinPath} -f ./bin/infection \
            "${extraArgs[@]}" \
            --threads=${infectionThreads} \
            --configuration=${infectionConfig} \
            --min-msi=${infectionMutationScoreIndicator} \
            --min-covered-msi=${infectionCoveredCodeMSI} \
            --log-verbosity=all
    fi
}

infectionExitCode=99
while (( infectionExitCode > 0 ))
do
    backupIFS=$IFS
    IFS=$standardIFS
    set +e

    rm -rf $varDir/infection/*
    runInfection
    infectionExitCode=$?
    set -e

    IFS=$backupIFS
    if (( $infectionExitCode > 0 ))
    then
        tryAgainOrAbort "Infection"
    fi
done

