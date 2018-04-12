pathsString=$(IFS=" " eval 'echo "${pathsToCheck[*]}"')

set +e
phpNoXdebug -f bin/phpcbf -- \
    --standard="$phpcsCodingStandardsPath" \
    --colors \
    --cache="$cacheDir"/phpcbf.cache \
    $pathsString
set +x
set -e
