annotationsExitCode=99
while (( annotationsExitCode > 0 ))
do
    set +e
    set -x
    phpNoXdebug bin/phpunit-check-annotation \
        "$testsDir"

    annotationsExitCode=$?
    set -e
    set +x
    if (( $annotationsExitCode > 0 ))
    then
        tryAgainOrAbort "PHPUnit Annotations Check"
    fi
done
set -e
