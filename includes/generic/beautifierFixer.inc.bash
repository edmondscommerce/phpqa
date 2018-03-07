set +e
phpNoXdebug -f bin/phpcbf -- \
    --standard="$phpcsCodingStandardsPath" \
    --colors \
    --cache="$cacheDir"/phpcbf.cache \
    "$srcDir" "$testsDir" "$binDir"
set +x
set -e
