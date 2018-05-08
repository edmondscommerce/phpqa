linksExitCode=99
while (( linksExitCode > 0 ))
do
    set +e
    phpNoXdebug -f bin/mdlinks
    linksExitCode=$?
    set +x
    set -e
    if (( linksExitCode > 0 ))
    then
        tryAgainOrAbort "Markdown Links Checker"
    fi
done