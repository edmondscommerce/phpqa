pathsStringArray=($(IFS=" " eval 'echo "${pathsToCheck[*]}"'))

pathsToIgnorePrefixed=()

for ignoreFile in "${pathsToIgnore[@]}"
do
    pathsToIgnorePrefixed+=( --exclude "$projectRoot/$ignoreFile")
done

phpNoXdebug -f bin/parallel-lint -- \
    "${pathsToIgnorePrefixed[@]}" \
    "${pathsToCheck[@]}"
set +x