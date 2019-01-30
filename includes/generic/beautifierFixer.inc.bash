set +e

phpNoXdebug -f bin/phpcbf -- \
    --standard="$phpcsCodingStandardsNameOrPath" \
    --colors \
    --cache="$cacheDir"/phpcbf.cache \
    ${pathsToCheck[@]}

set -e
