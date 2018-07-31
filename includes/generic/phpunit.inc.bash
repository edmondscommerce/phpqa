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
    declare -a rerunFilter
    rerunFilter=(" ")
# THIS ISNT WORKING AT THE MOMENT - COMMENTING OUT FOR NOW
#    if phpunitReRunFailedOrFull
#    then
#        #set no glob
#        set -f
#        rerunFilterPattern=$(phpNoXdebug bin/phpunit-runfailed-filter)
#        if [[ "$rerunFilterPattern" != '/.*/' ]]
#        then
#            rerunFilter+=( --filter $IFS $rerunFilterPattern)
#            phpunitFailedOnlyFiltered=1
#        fi
#    fi
    noCoverage=(" ")
    if [[ "1" != "$phpUnitCoverage" ]]
    then
        noCoverage+=( --no-coverage )
    fi
    set +e
    set -x
    phpUnitQuickTests="$phpUnitQuickTests" $phpCmd -f $phpunitPath \
        -- \
        ${paratestConfig[@]} \
        -c ${phpUnitConfigPath} \
        ${rerunFilter[@]} \
        ${noCoverage[@]} \
        --enforce-time-limit \
        --fail-on-risky \
        --fail-on-warning \
        --disallow-todo-tests

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
