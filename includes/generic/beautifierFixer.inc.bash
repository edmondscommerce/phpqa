set +e
phpNoXdebug -f bin/phpcbf -- \
    --standard="$phpcsCodingStandardsPath" \
    --colors \
    --cache="$cacheDir"/phpcbf.cache \
    ${pathsToCheck[@]}
set +x
set -e
