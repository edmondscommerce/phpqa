# project tests folder
testsDir=""

# project src folder
srcDir="app"

# project bin dir
binDir=""

# project var dir, sub directory for qa cache files and output files
varDir="$projectRoot/var/qa";

cacheDir="$projectRoot/cache/qa";

# the path in this library for default config
defaultConfigPath="$(readlink -f $DIR/../configDefaults/)"

# PHPStan configs
phpstanConfigPath="$(configPath phpstan.neon)"

#PHP Mess Detector Configs
phpmdConfigPath="$(configPath phpmd/ruleset.xml)"

# coding Standard for checking
# checks for a folder called 'condingStandards' in the $projectConfigPath, falls back to the PSR2 standards
phpcsCodingStandardsPath=$(configPath \
    codingStandards \
    $projectRoot/vendor/squizlabs/php_codesniffer/src/Standards/PSR2
)

# should coding standards warnings be a fail?
phpcsFailOnWarning=0

##PHPUnit Configs

# PHPUnit Quick Tests - optional skip slow tests
phpUnitQuickTests=${phpUnitQuickTests:-1}

# PHPUnit Coverage - if enabled, tests will run with Xdebug and generate coverage
phpUnitCoverage=${phpUnitCoverage:-1}

if [[ -f $projectRoot/phpunit.xml ]]
then
    # Project root phpunit.xml trumps everything else
    phpUnitConfigPath=$projectRoot/phpunit.xml
else
    if [[ "1" == "$phpUnitCoverage" ]]
    then
        phpUnitConfigPath=$(configPath phpunit-with-coverage.xml)
    else
        phpUnitConfigPath=$(configPath phpunit.xml)
    fi
fi
