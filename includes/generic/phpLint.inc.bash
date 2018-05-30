pathsStringArray=($(IFS=" " eval 'echo "${pathsToCheck[*]}"'))

pathsToIgnorePrefixed=()

for ignoreFile in "${pathsToIgnore[@]}"
do
    pathsToIgnorePrefixed+=( --exclude "$projectRoot/$ignoreFile")
done

phpLintExitCode=99
set +e
while (( phpLintExitCode > 0 ))
do
    set -x
    phpNoXdebug -f bin/parallel-lint -- \
    "${pathsToIgnorePrefixed[@]}" \
    "${pathsToCheck[@]}"
    phpLintExitCode=$?
    set +x
    if (( phpLintExitCode > 0 ))
    then
        tryAgainOrAbort "PHP Lint"
    fi
done
set -e