pathsToCheck=""

if [[ -d "$projectRoot/app/code" ]]
then
    if [[ "$pathsToCheck" == "" ]]
    then
        pathsToCheck="$projectRoot/app/code"
    else
        pathsToCheck="$pathsToCheck,$projectRoot/app/code"
    fi
fi
if [[ -d "$projectRoot/app/design" ]]
then
    if [[ "$pathsToCheck" == "" ]]
    then
        pathsToCheck="$projectRoot/app/design"
    else
        pathsToCheck="$pathsToCheck,$projectRoot/app/design"
    fi
fi
if [[ -d "$projectRoot/vendor" ]]
then
    if [[ "$pathsToCheck" == "" ]]
    then
        pathsToCheck="$projectRoot/vendor"
    else
        pathsToCheck="$pathsToCheck,$projectRoot/vendor"
    fi
fi

set +e
phpMdExitCode=99
while (( phpMdExitCode > 0 ))
do
set -x
    phpNoXdebug -f bin/phpmd -- \
        $pathsToCheck \
        text \
        "$phpmdConfigPath" \
        --suffixes php,phtml \
        --exclude "vendor/braintree,\
vendor/colinmollenhour,\
vendor/composer,\
vendor/container-interop,\
vendor/doctrine,\
vendor/dotmailer,\
vendor/edmondscommerce,\
vendor/friendsofphp,\
vendor/ircmaxell,\
vendor/jakub-onderka,\
vendor/johnkary,\
vendor/justinrainbow,\
vendor/league,\
vendor/lusitanian,\
vendor/magento,\
vendor/monolog,\
vendor/myclabs,\
vendor/oyejorge,\
vendor/paragonie,\
vendor/pdepend,\
vendor/pelago,\
vendor/phar-io,\
vendor/phpdocumentor,\
vendor/phploc,\
vendor/phpmd,\
vendor/phpseclib,\
vendor/phpspec,\
vendor/phpstan,\
vendor/phpunit,\
vendor/psr,\
vendor/ramsey,\
vendor/sebastian,\
vendor/seld,\
vendor/shopialfb,\
vendor/sjparkinson,\
vendor/squizlabs,\
vendor/symfony,\
vendor/tedivm,\
vendor/temando,\
vendor/theseer,\
vendor/tubalmartin,\
vendor/webmozart,\
vendor/zendframework" \
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
