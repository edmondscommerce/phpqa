pathsToCheck=();

if [[ -d "$projectRoot/app/code" ]]
then
    pathsToCheck+=("$projectRoot/app/code")
fi
if [[ -d "$projectRoot/app/design" ]]
then
    pathsToCheck+=("$projectRoot/app/design")
fi



pathsToIgnore=();
pathsToIgnore=("placeholder-ignore-path");