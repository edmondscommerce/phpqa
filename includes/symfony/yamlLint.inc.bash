yamlLintExistCode=99
set +e
while (( yamlLintExistCode > 0 ))
do
    phpNoXdebug -f ./bin/console -- lint:yaml app
    yamlLintExistCode=$?
    if (( yamlLintExistCode > 0 ))
    then
        tryAgainOrAbort "Yaml Lint"
    fi
done
set -e
