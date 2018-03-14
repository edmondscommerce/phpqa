pathsToCheck=""

if [[ -d "$projectRoot/app/code" ]]
then
    pathsToCheck="$pathsToCheck $projectRoot/app/code"
fi
if [[ -d "$projectRoot/app/design" ]]
then
    pathsToCheck="$pathsToCheck $projectRoot/app/design"
fi
if [[ -d "$projectRoot/vendor" ]]
then
    pathsToCheck="$pathsToCheck $projectRoot/vendor"
fi


# Check if MEQP is composer installed

if [[ ! -d $projectRoot/vendor/magento/marketplace-eqp ]]
then
    echo "Make sure the MEQP standards are installed by running"
    echo "composer require magento/marketplace-eqp"
    exit 1
fi


phpNoXdebug -f bin/phpcs -- \
    --config-set ignore_warnings_on_exit "$phpcsFailOnWarning" \
    --config-set installed_paths "$projectRoot/vendor/magento/marketplace-eqp/"
set +x
set +e
phpcsExitCode=99
while (( phpcsExitCode > 0 ))
do
    set -x
    eval phpNoXdebug -f bin/phpcs -- \
        --config-set installed_paths "$projectRoot/vendor/magento/marketplace-eqp/" \
        --config-set m2-path $projectRoot \
        --standard="MEQP2" \
        --colors \
        --cache="$cacheDir"/phpcs.cache \
        -s \
        --report-full \
        --report-summary \
        $pathsToCheck
    phpcsExitCode=$?
    set +x
    if (( phpcsExitCode > 0 ))
    then
        tryAgainOrAbort "Code Sniffer"
    fi

done