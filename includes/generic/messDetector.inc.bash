set +e
phpMdExitCode=99
while (( phpMdExitCode > 0 ))
do
    phpNoXdebug -f bin/phpmd -- \
        "$srcDir","$testsDir","$binDir" \
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
