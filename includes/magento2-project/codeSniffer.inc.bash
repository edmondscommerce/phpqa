pathsString=$(IFS=" " eval 'echo "${pathsToCheck[*]}"')
ignoreString=$(IFS="," eval 'echo "${pathsToIgnore[*]}"')

# Check if MEQP is composer installed

if [[ ! -d $projectRoot/vendor/magento/marketplace-eqp ]]
then
    echo "ERROR: The MEQP Coding Standards are not installed"
    echo ""
    echo "Make sure the MEQP standards are installed by adding it to Magento's composer dependencies"
    echo ""
    echo "** MEQP CodeSniffer Incompatibilities **"
    echo "At the time of writing, all versioned magento/marketplace-eqp require a version"
    echo "of CodeSniffer (2.6.2) that's incompatible with the one in Magento's requirements"
    echo "An in-development version of MEQP resolves this, but is not yet available on Packagist"
    echo ""
    echo "You must add a new repository to Magento's composer.json:"
    echo '
"repositories": {
    ...
    "marketplace-eqp": {
        "type": "vcs",
        "url": "https://github.com/magento/marketplace-eqp"
    }
    ...
}
    '
    echo ""
    echo "And then require-dev the master branch:"
    echo '
"require-dev": {
    ...
    "magento/marketplace-eqp": "dev-master"
    ...
}
    '
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
    set +x
    echo "Setting PHPCS Config"
    eval phpNoXdebug -f bin/phpcs -- \
        --config-set installed_paths "$projectRoot/vendor/magento/marketplace-eqp/" \
        --config-set m2-path $projectRoot

    set +x
    echo "Running PHPCS..."
    eval phpNoXdebug -f bin/phpcs -- \
        --standard="MEQP2" \
        --colors \
        --cache="$cacheDir"/phpcs.cache \
        -s \
        --report-full \
        --report-summary \
        --ignore=$ignoreString \
        $pathsString
    phpcsExitCode=$?
    set +x
    if (( phpcsExitCode > 0 ))
    then

    echo
    echo "PHPCBF can automatically fix some violations"
    echo -n "Do you want to run PHPCBF on $scanPath? (y/n) "
    read runBeautifier
    if [[ $runBeautifier == "y" ]]
    then
        runTool beautifierFixer
    fi

        tryAgainOrAbort "Code Sniffer"
    fi

done
