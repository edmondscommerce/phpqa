#!/usr/bin/env bash

extraArgs=( -- )
if [[ "1" == "$infectionOnlyCovered" && "0" ]]
then
    extraArgs+=( --only-covered )
fi
if [[ "0" == "${phpunitFailedOnlyFiltered:-0}" ]]
then
    extraArgs+=( --coverage=$varDir/phpunit_logs )
    extraArgs+=( --test-framework-options=" --no-coverage -c ${phpUnitConfigPath} " )
else
    extraArgs+=( --test-framework-options=" -c ${phpUnitConfigPath} " )
fi

infectionExitCode=99
while (( infectionExitCode > 0 ))
do
    set +e
    set -x
    rm -rf $varDir/infection/*
    phpNoXdebug -f ./bin/infection \
        ${extraArgs[@]} \
        --threads=${infectionThreads} \
        --configuration=${infectionConfig} \
        --min-msi=${infectionMutationScoreIndicator} \
        --min-covered-msi=${infectionCoveredCodeMSI}

    infectionExitCode=$?
    set -e
    set +x
    if (( $infectionExitCode > 0 ))
    then
        tryAgainOrAbort "Infection"
    fi
done
