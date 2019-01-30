pathsString=$(IFS="," eval 'echo "${pathsToCheck[*]}"')
ignoreString=$(IFS="," eval 'echo "${pathsToIgnore[*]}"')

set +e
phpMdExitCode=99
while (( phpMdExitCode > 0 ))
do
    output=$(phpNoXdebug -f bin/phpmd -- \
        $pathsString \
        text \
        "$phpmdConfigPath" \
        --suffixes php,phtml \
        --exclude $ignoreString)
        if [[ "$output" == "" ]]
        then
            phpMdExitCode=0;
        else
            echo "$output" \
                | sort -u \
                | sed G \
                | sed -e 's#p:#p\nLine: #' \
                | sed -e 's#\t\{1,\}#\nMessage: #' \
                | sed -e 's#\. #\.\n#'
            phpMdExitCode=1
        fi
    if (( phpMdExitCode > 0 ))
    then
        tryAgainOrAbort "PHP Mess Detector"
    fi
done
set -e
