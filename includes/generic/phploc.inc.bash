pathsString=$(IFS=" " eval 'echo "${pathsToCheck[*]}"')

phpNoXdebug -f bin/phploc $pathsString
set +x