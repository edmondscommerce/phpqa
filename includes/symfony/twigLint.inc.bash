twigLintExistCode=99
set +e
while (( twigLintExistCode > 0 ))
do
    phpNoXdebug -f bin/console --- lint:twig templates
    twigLintExistCode=$?
    if (( twigLintExistCode > 0 ))
    then
        tryAgainOrAbort "Twig Lint"
    fi
done
set -e
