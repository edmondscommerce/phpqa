#!/usr/bin/env bash
infectionExitCode=99
while (( infectionExitCode > 0 ))
do
    set -x
    phpNoXdebug ./bin/infection \
        --threads=${numberOfCores} \
        --configuration=${infectionConfig} \
        --min-msi=${mutationScoreIndicator} \
        --min-covered-msi=${coveredCodeMSI} \
        --coverage=$varDir/phpunit_logs \
        --test-framework-options=" -c ${phpUnitConfigPath} "

    infectionExitCode=$?
    set -e
    set +x
    set +f
    if (( $infectionExitCode > 0 ))
    then
        if (( $infectionExitCode > 2 ))
        then
            printf "\n\n\nPHPUnit Crashed\n\nRunning again with Debug mode...\n\n\n"
            qaQuickTests="$phpUnitQuickTests" phpNoXdebug -f bin/phpunit -- "$testsDir" --debug
            set +x
        fi
        tryAgainOrAbort "PHPUnit Tests"
    fi
done
