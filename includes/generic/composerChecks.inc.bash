set +e

phpNoXdebug -f $(which composer) -- diagnose

set -e

phpNoXdebug -f $(which composer) -- dump-autoload
