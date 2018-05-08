phpLintExitCode=99
set +e
while (( phpLintExitCode > 0 ))
do
    phpNoXdebug -f bin/parallel-lint -- $srcDir $testsDir $binDir
    phpLintExitCode=$?
    set +x
    if (( phpLintExitCode > 0 ))
    then
        tryAgainOrAbort "PHP Lint"
    fi
done
set -e