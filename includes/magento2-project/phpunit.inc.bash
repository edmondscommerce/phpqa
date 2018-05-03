set +e
phpCmd=phpNoXdebug
if [[ "1" == "$phpUnitCoverage" ]]
then
    phpCmd=\php
fi

qaQuickTests="$phpUnitQuickTests" $phpCmd -f bin/phpunit \
    -- \
    -c $phpUnitConfigPath

phpunitExitCode=$?
set +x
if (( $phpunitExitCode > 0 ))
then
    if (( $phpunitExitCode > 2 ))
    then
        printf "\n\n\nPHPUnit Crashed\n\nRunning again with Debug mode...\n\n\n"
        qaQuickTests="$phpUnitQuickTests" phpNoXdebug -f bin/phpunit -- --debug
        set +x
    fi
    exit $phpunitExitCode
fi
set -e
