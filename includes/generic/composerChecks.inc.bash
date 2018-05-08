set +e
phpNoXdebug -f $(which composer) -- diagnose
set +x
set -e

phpNoXdebug -f $(which composer) -- dump-autoload
set +x