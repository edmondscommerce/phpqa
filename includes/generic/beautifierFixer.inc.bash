set +e
set -x
phpNoXdebug -f bin/phpcbf -- \
    --standard="$phpcsCodingStandardsNameOrPath" \
    --colors \
    --cache="$cacheDir"/phpcbf.cache \
    ${pathsToCheck[@]}
set +x
set -e
