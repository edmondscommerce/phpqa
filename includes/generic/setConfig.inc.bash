# Skip long running tests if globally set to 1
phpqaQuickTests=${phpqaQuickTests:-0}

# the path in the project to check for config
projectConfigPath="$projectRoot/qaConfig/"

# project tests folder
testsDir="$(findTestsDir)"

# project src folder
srcDir="$(findSrcDir)"

# project bin dir
binDir="$(findBinDir)"

# An array of paths that are to be checked
pathsToCheck=()
pathsToCheck+=($testsDir)
pathsToCheck+=($srcDir)
pathsToCheck+=($binDir)

# An array of paths that are to be ignored
pathsToIgnore=()
pathsToIgnore+=("placeholder-ignore-item")

# project var dir, sub directory for qa cache files and output files
varDir="$projectRoot/var/qa";

cacheDir="$projectRoot/cache/qa";

# the path in this library for default config
defaultConfigPath="$(readlink -f ./../configDefaults/)"

# configPath function can only be used after this point

# PSR4 validation
psr4IgnoreListPath="$(configPath psr4-validate-ignore-list.txt)"
readarray psr4IgnoreList < "$psr4IgnoreListPath"

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
phpUnitQuickTests=${phpUnitQuickTests:-0}

# PHPUnit Coverage - default disabled
# if enabled, tests will run with Xdebug and generate coverage (which is a lot slower)
phpUnitCoverage=${phpUnitCoverage:-0}

# How many minutes after a failed PHPUnit run you can retry failed only
phpunitRerunTimeoutMins=${phpunitRerunTimeoutMins:-5}

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

# If a CI variable is set, we use that, otherwise default to false.
# Travis-CI sets a CI variable. You can easily set this in any other CI system
# The value should the the string 'true' if this is CI
CI=${CI:-'false'}