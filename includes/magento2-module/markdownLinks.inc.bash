

if [[ -f $projectRoot/README.md ]]
then
    phpNoXdebug -f bin/mdlinks
    set +x
else
    echo "The Markdown Links check requires a README.md in the root of the repository"
    echo "You must create a README.md to proceed
    "
    exit 1;
fi