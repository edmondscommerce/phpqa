pathsString=$(IFS=" " eval 'echo "${pathsToCheck[*]}"')
ignoreString=$(IFS="," eval 'echo "${pathsToIgnore[*]}"')

phpNoXdebug -f bin/phpcs -- \
    --config-set ignore_warnings_on_exit "$phpcsFailOnWarning"

set +e
phpcsExitCode=99
while (( phpcsExitCode > 0 ))
do
    phpNoXdebug -f bin/phpcs -- \
        --standard="$phpcsCodingStandardsNameOrPath" \
        --colors \
        --cache="$cacheDir"/phpcs.cache \
        -s \
        --report-full \
        --report-summary \
        --ignore=$ignoreString \
        ${pathsToCheck[@]}
    phpcsExitCode=$?

    if (( phpcsExitCode > 0 ))
    then
        tryAgainOrAbort "Code Sniffer"
    fi

done
