# project tests folder
testsDir="$(findTestsDir)"

# project src folder - hard coded for Laravel
srcDir="$projectRoot/app"

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