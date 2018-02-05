#### CONFIG ##################################################

# project root directory
projectRoot="$(realpath ./../)"

# the path in the project to check for config
projectConfigPath="${projectRoot}/qaConfig/"

# project tests folder
testsDir="$(find ${projectRoot} -maxdepth 1 -type d \( -name test -o -name tests \) | head -n1)"

# project src folder
srcDir="${projectRoot}/../app/code"

# project bin dir
binDir="$(find ${projectRoot} -maxdepth 2 -type d -path "*vendor*" -name bin | head -n1)"

# project var dir, sub directory for qa cache files and output files
varDir="${projectRoot}/var/qa";

cacheDir="${projectRoot}/cache/qa";

# the path in this library for default config
defaultConfigPath="$(realpath ./../configDefaults/)"

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

runStrictType=0

# should coding standards warnings be a fail?
phpcsFailOnWarning=0

# PHPUnit Quick Tests
phpUnitQuickTests=${phpUnitQuickTests:-1}

#### PROCESS #################################################
