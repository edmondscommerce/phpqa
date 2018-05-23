# Skip long running tests if globally set to 1
phpqaQuickTests=${phpqaQuickTests:-0}

# Allow uncommitted changes check by default
skipUncommittedChangesCheck=${skipUncommittedChangesCheck:-0}

# the path in the project to check for config
projectConfigPath="$projectRoot/qaConfig/"

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
phpcsCodingStandardsPath="PSR2"

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

if [[ "1" == "$phpUnitCoverage" ]]
then
    phpUnitConfigPath=$(configPath phpunit-with-coverage.xml)
else
    phpUnitConfigPath=$(configPath phpunit.xml)
fi

# If a CI variable is set, we use that, otherwise default to false.
# Travis-CI sets a CI variable. You can easily set this in any other CI system
# The value should the the string 'true' if this is CI
CI=${CI:-'false'}

## Infection options
# Let's use infection by default - set this to 0 to use phpunit instead
useInfection=${useInfection:-1}
# This is the path to our configuration
infectionConfig=$(configPath infection.json)
# Speeds up the tests https://infection.github.io/guide/command-line-options.html#threads
# Can cause issues if the test rely on the database
numberOfCores=$(grep -c ^processor /proc/cpuinfo)
# See here https://infection.github.io/guide/index.html#Mutation-Score-Indicator-MSI and here
# for more details about this
mutationScoreIndicator=${mutationScoreIndicator:-60}
# See here https://infection.github.io/guide/index.html#Covered-Code-Mutation-Score-Indicator
coveredCodeMSI=${coveredCodeMSI:-90}
