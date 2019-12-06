# Skip long running tests if globally set to 1
phpqaQuickTests=${phpqaQuickTests:-0}

# Allow uncommitted changes check by default
skipUncommittedChangesCheck=${skipUncommittedChangesCheck:-0}

# the path in the project to check for config
projectConfigPath="$projectRoot/qaConfig/"

# project var dir, sub directory for qa cache files and output files
varDir="$projectRoot/var/qa";

cacheDir="$varDir/cache";

noXdebugConfigPath="$varDir/phpqa-no-xdebug.ini"

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
# For PSR2, you need to override this and set the value to "PSR2"
phpcsCodingStandardsNameOrPath="$(configPath codingStandard)"

# should coding standards warnings be a fail?
phpcsFailOnWarning=0

##PHPUnit Configs

#Iterative Mode - prioritises runnign failed tests and stops on first error
phpUnitIterativeMode=${phpUnitIterativeMode:-0}

# PHPUnit Quick Tests - optional skip slow tests
phpUnitQuickTests=${phpUnitQuickTests:-0}

# PHPUnit Coverage - default disabled
# if enabled, tests will run with Xdebug and generate coverage (which is a lot slower)
phpUnitCoverage=${phpUnitCoverage:-1}

# Can only generate coverage if Xdebug is enabled
if [[ "1" != "$xdebugEnabled" ]]
then
    phpUnitCoverage=0
fi

# Now check if we are generating coverage and configure the correct file to include
phpUnitConfigPath=$(configPath phpunit.xml)

## Infection options
# Let's use infection by default
# If no PHPUnit coverage though, we cant use it
useInfection=${useInfection:-1}
if [[ "0" == "$xdebugEnabled" || "0" == "$phpUnitCoverage" ]]
then
    useInfection=0
fi

# This is the path to our configuration
infectionConfig=$(configPath infection.json)
# Speeds up the tests https://infection.github.io/guide/command-line-options.html#threads
# Can cause issues if the test rely on the database
infectionThreads=${infectionThreads:-$(grep -c ^processor /proc/cpuinfo)}
# See here https://infection.github.io/guide/index.html#Mutation-Score-Indicator-MSI and here
# for more details about this
infectionMutationScoreIndicator=${mutationScoreIndicator:-60}
# See here https://infection.github.io/guide/index.html#Covered-Code-Mutation-Score-Indicator
infectionCoveredCodeMSI=${coveredCodeMSI:-80}
# Only Covered
infectionOnlyCovered=${infectionOnlyCovered:-0}

composerRequireCheckerConfig=$(configPath composerRequireChecker.json)

phpCsConfigPath=$(configPath php_cs.php)
phpCsCacheFile="$varDir/cache/php_cs.cache"

# If a CI variable is set, we use that, otherwise default to false.
# Travis-CI sets a CI variable. You can easily set this in any other CI system
# The value should the the string 'true' if this is CI
CI=${CI:-'false'}
