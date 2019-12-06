set -e
for phpCsPathToCheck in "${pathsToCheck[@]}"; do
  printf "\nRunning PHP CS Fixer For Path:\n - $phpCsPathToCheck\n\n"
  ./bin/php-cs-fixer \
    --config="$phpCsConfigPath" \
    --cache-file="$phpCsCacheFile.$(echo $phpCsPathToCheck | md5sum -)" \
    --allow-risky=yes \
    --show-progress=dots \
    fix \
    $phpCsPathToCheck
done
