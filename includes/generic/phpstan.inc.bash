set +e
phpStanExitCode=99
phpStanMemoryLimit=${phpStanMemoryLimit:-256M}
phpstanNoProgress=()
if [[ "true" == "$CI" ]]; then
  phpstanNoProgress+=(--no-progress)
fi
while ((phpStanExitCode > 0)); do
  phpNoXdebug -d memory_limit=${phpStanMemoryLimit} -f bin/phpstan.phar -- analyse ${pathsToCheck[@]} -c "$phpstanConfigPath" ${phpstanNoProgress[@]:-}
  phpStanExitCode=$?

  #exit code 0 = fine, 1 = ran fine but found errors, else it means it crashed
  if ((phpStanExitCode > 1)); then
    printf "\n\n\nPHPStan Crashed!!....\n\nrunning again with debug mode:\nWhere ever it stops is probably a fatal PHP error\n\n"
    eval phpNoXdebug -f bin/phpstan.phar -- analyse $pathsStringArray -c "$phpstanConfigPath" --debug
    exit 1
  fi
  if ((phpStanExitCode > 0)); then
    tryAgainOrAbort "PHPStan"
  fi
done
set -e
