if [[ "" == "$specifiedPath" ]]
then
    echo "Code Beautifier and Fixer modifies code"
    echo "This tool does not run on an entire Magento 2 codebase. You should instead run it on specific modules you wish to modify."
else
    echo
    echo "PHPCBF can automatically fix some violations"
    echo -n "Do you want to run PHPCBF on $specifiedPath? (y/n) "
    read runBeautifier
    if [[ $runBeautifier == "y" ]]
    then
        source $DIR/../includes/generic/beautifierFixer.inc.bash
    fi
fi