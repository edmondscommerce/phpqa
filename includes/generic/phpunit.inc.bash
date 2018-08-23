# Note the phpUnitQuickTests=$phpUnitQuickTests
# this sets a config variable which you can then use
# to allow tests to run less thoroughly but more quickly
# @see https://github.com/edmondscommerce/phpqa#quick-tests

phpCmd=phpNoXdebug
if [[ "1" == "$phpUnitCoverage" ]]
then
    phpCmd=\php
fi
phpunitPath=bin/phpunit
paratestConfig=
echo "Checking for paratest"
if [[ -f bin/paratest ]]
then
    echo "Found paratest, using this instead of standard bin/phpunit"
    phpunitPath=bin/paratest
    paratestConfig=(--phpunit bin/phpunit)
fi
phpunitFailedOnlyFiltered=0
phpunitExitCode=99
phpunitLogFilePath="$varDir/phpunit_logs/phpunit.junit.xml"
while (( phpunitExitCode > 0 ))
do
    extraConfigs=(" ")
    if [[ "1" == "$phpUnitIterativeMode" ]]
    then
        # Uniterate mode - order by defects, stop on first error, no coverage and enforce time limits
        echo
        echo "Uniterate Mode - Iterative Testing with Fast Failure"
        echo "----------------------------------------------------"
        echo
        extraConfigs+=( --order-by=depends,defects )
        extraConfigs+=( --stop-on-failure --stop-on-error --stop-on-defect --stop-on-warning )
        extraConfigs+=( --no-coverage )
        extraConfigs+=( --enforce-time-limit )
    elif [[ "1" != "$phpUnitCoverage" ]]
    then
        # No Coverage mode - do not generate coverage, do enforce time limits
        extraConfigs+=( --no-coverage )
        extraConfigs+=( --enforce-time-limit )
    elif [[ "false" != "${CI:-'false'}" ]]
        # When in CI and generating coverage - stop on first error, do not enforce time limits
        extraConfigs+=( --stop-on-failure --stop-on-error --stop-on-defect --stop-on-warning )
    else
        # Default mode - do generate coverage if configured to do so, do enforce time limits
        extraConfigs+=( --enforce-time-limit )
    fi
    set +e
    set -x
    phpUnitQuickTests="$phpUnitQuickTests" $phpCmd -f $phpunitPath \
        -- \
        ${paratestConfig[@]} \
        -c ${phpUnitConfigPath} \
        ${extraConfigs[@]} \
        --fail-on-risky \
        --fail-on-warning \
        --disallow-todo-tests \
        --log-junit "$phpunitLogFilePath"

    phpunitExitCode=$?
    set -e
    set +x
    if [[ "" != "$(grep '<testsuites/>' $phpunitLogFilePath)" || ! -f  $phpunitLogFilePath ]]
    then
        echo "

        ERROR - no tests have been run!

        Please ensure you have at least one valid test suite configured in your phpunit.xml file

        "
        phpunitExitCode=1
    fi

    if (( $phpunitExitCode > 0 ))
    then
        if (( $phpunitExitCode > 2 ))
        then
            printf "\n\n\nPHPUnit Crashed\n\nRunning again with Debug mode...\n\n\n"
            qaQuickTests="$phpUnitQuickTests" phpNoXdebug -f bin/phpunit -- "$testsDir" --debug
            set +x
        fi
        tryAgainOrAbort "PHPUnit Tests"
    fi
done
set -e
