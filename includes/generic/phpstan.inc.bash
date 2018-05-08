set +e
phpStanExitCode=99
while (( phpStanExitCode > 0 ))
do
    set -x
    phpNoXdebug -f bin/phpstan  -- analyse "$srcDir" "$testsDir" "$binDir" -l7 -c "$phpstanConfigPath"
    phpStanExitCode=$?
    set +x
    #exit code 0 = fine, 1 = ran fine but found errors, else it means it crashed
    if (( phpStanExitCode > 1 ))
    then
        printf "\n\n\nPHPStan Crashed....\n\nrunning again with debug mode:\n\n\n"
        phpNoXdebug -f bin/phpstan  -- analyse "$srcDir" "$testsDir" "$binDir" -l7 -c "$phpstanConfigPath" --debug
        exit 1
    fi
    if (( phpStanExitCode > 0 ))
    then
        tryAgainOrAbort "PHPStan"
    fi
done
set -e