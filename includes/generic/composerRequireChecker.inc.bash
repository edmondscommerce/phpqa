#!/usr/bin/env bash

composerRequireCheckExitCode=99
while (( composerRequireCheckExitCode > 0 ))
do
    set +e
    phpNoXdebug ./bin/composer-require-checker check --config-file="${composerRequireCheckerConfig}" -- "${projectRoot}/composer.json";
    composerRequireCheckExitCode=$?
    set -e
    if (( $composerRequireCheckExitCode > 0 ))
    then
        echo "

To fix these issues, you probably need to add things to your 'require' section in your projects composer.json

You might do this by moving things from 'require-dev', or it could be things that are brought in by your dependencies that you need to add.

The ones that say 'ext-json' or similar, you just need to add '\"ext-json\": \"*\"'

Of course the other option is that you refactor your code and stop using your dev dependencies in your production code

        "
        tryAgainOrAbort "Composer Require Check"
    fi
done
