codeDir="$projectRoot/app/code"
designDir="$projectRoot/app/dir"
vendorDir="$projectRoot/vendor"

# project var dir, sub directory for qa cache files and output files
varDir="$projectRoot/var/qa";

cacheDir="$projectRoot/cache/qa";

# the path in this library for default config
defaultConfigPath="$(readlink -f $DIR/../configDefaults/)"

# PHPStan configs
phpstanConfigPath="$(configPath phpstan.neon)"

#PHP Mess Detector Configs
phpmdConfigPath="$projectRoot/dev/tests/static/testsuite/Magento/Test/Php/_files/phpmd/ruleset.xml"

# coding Standard for checking
# checks for a folder called 'condingStandards' in the $projectConfigPath, falls back to the PSR2 standards
phpcsCodingStandardsPath="$projectRoot/vendor/magento/marketplace-eqp/MEQP2/"

# should coding standards warnings be a fail?
phpcsFailOnWarning=0

##PHPUnit Configs

# PHPUnit Quick Tests - optional skip slow tests
phpUnitQuickTests=${phpUnitQuickTests:-1}

# PHPUnit Coverage - if enabled, tests will run with Xdebug and generate coverage
phpUnitCoverage=${phpUnitCoverage:-1}

if [[ -f $projectRoot/dev/tests/unit/phpunit.xml ]]
then
    # Project root phpunit.xml trumps everything else
    phpUnitConfigPath=$projectRoot/dev/tests/unit/phpunit.xml
elif [[ -f $projectRoot/dev/tests/unit/phpunit.xml ]]
then
    # Project root phpunit.xml trumps everything else
    phpUnitConfigPath=$projectRoot/dev/tests/unit/phpunit.xml.dist
fi