if [[ -f $projectRoot/README.md ]]
then
    phpNoXdebug -f bin/mdlinks
    set +x
else
    echo "Skipping Markdown Links check because there's no README.md"
fi