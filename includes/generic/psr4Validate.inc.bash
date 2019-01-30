set +e
psr4ExitCode=99
while (( psr4ExitCode > 0 ))
do

    phpNoXdebug -f ./bin/psr4-validate ${psr4IgnoreList[*]}
    psr4ExitCode=$?

    if (( psr4ExitCode > 0 ))
    then
        tryAgainOrAbort "PSR-4 Validation"
    fi
done