pathsToCheck=""

if [[ -d "$projectRoot/app/code" ]]
then
    pathsToCheck="$pathsToCheck,$projectRoot/app/code"
fi
if [[ -d "$projectRoot/app/design" ]]
then
    pathsToCheck="$pathsToCheck,$projectRoot/app/design"
fi
if [[ -d "$projectRoot/vendor" ]]
then
    pathsToCheck="$pathsToCheck,$projectRoot/vendor"
fi

set +e
phpMdExitCode=99
while (( phpMdExitCode > 0 ))
do
    phpNoXdebug -f bin/phpmd -- \
        $pathsToCheck \
        text \
        "$phpmdConfigPath" \
        --suffixes php,phtml \
        | sort -u \
        | sed G \
        | sed -e 's#p:#p\nLine: #' \
        | sed -e 's#\t\{1,\}#\nMessage: #' \
        | sed -e 's#\. #\.\n#'
    phpMdExitCode=$?
    set +x
    if (( phpMdExitCode > 0 ))
    then
        tryAgainOrAbort "PHP Mess Detector"
    fi
done
set +x
set -e
