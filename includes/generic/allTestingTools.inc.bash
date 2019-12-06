echo "

Running PHPUnit Tests
---------------------
"
if [[ "$phpqaQuickTests" == "1" ]]
then
    echo "Skipping PHP Unit & Infection because \$phpqaQuickTests=1"
else
    echo "
Running tests using PhpUnit
---------------------------
"
    runTool phpunit

    if [[ "${TRAVIS:-'false'}" == "true" && "$xdebugEnabled" == "1" ]]
    then
        echo "Disabling Xdebug After PHPUnit on Travis"
        phpenv config-rm xdebug.ini
    fi

    if [[ "$useInfection" == "1" ]]
    then
        echo "
Running tests using Infection
-----------------------------
"
        runTool infection
    fi
fi
