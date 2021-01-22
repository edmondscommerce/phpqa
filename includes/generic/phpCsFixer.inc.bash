csFixerExitCode=99
while ((csFixerExitCode > 1)); do
  set +e
  phpNoXdebug -f ./bin/php-cs-fixer -- \
    --config="$phpCsConfigPath" \
    --cache-file="$phpCsCacheFile.$(echo $phpCsPathToCheck | md5sum -)" \
    --allow-risky=yes \
    --show-progress=dots \
    -vvv \
    fix
  csFixerExitCode=$?
  set -e
  if ((csFixerExitCode > 0)); then
    tryAgainOrAbort "PHP CS Fixer"
  fi
done
