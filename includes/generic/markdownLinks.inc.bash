if [[ -f $projectRoot/README.md ]]
then
    linksExitCode=99
    while (( linksExitCode > 0 ))
    do
        set +e

        phpNoXdebug -f bin/mdlinks
        linksExitCode=$?

        set -e
        if (( linksExitCode > 0 ))
        then
            tryAgainOrAbort "Markdown Links Checker"
        fi
    done
else
    echo "The Markdown Links check requires a README.md in the root of the repository"
    echo "You must create a README.md to proceed
    "
    exit 1;
fi
