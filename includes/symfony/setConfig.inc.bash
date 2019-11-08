source ${DIR}/../includes/generic/setConfig.inc.bash

## Directories that should have the twig lint tool run against them - see php bin/console help lint:twig
twigDirectories="${projectRoot}/templates"

## Directories that should have the yaml lint tool run against them - see php bin/console help lint:yaml
yamlDirectories="${projectRoot}/config"
