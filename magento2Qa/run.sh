#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"

magentoDir=${DIR}

while [[ ! -e ${magentoDir}/app/etc/env.php ]]
do
    if [[ "${magentoDir}" == '/' ]]
    then
        echo "Could not find a magento directory - quitting"
        exit 5;
    fi
    magentoDir=`realpath "${magentoDir}/../"`
done

if [[ $1 = "" ]]
then
    echo "You must pass in the path of code to check"
    exit 5;
fi

# Checking both an absolute or relative path
rawPath=${1}
if [[ ! -e ${rawPath} ]]
then
    # Relative to magento root?
    rawPath=${magentoDir}"/"${1}
    if [[ ! -e ${rawPath} ]]
    then
      echo "You must pass a valid file path to the script"
      exit 5
    fi
fi
fullPath=`realpath ${rawPath}`
# Patch complains about absolute paths, so lets get a relative one
pathToCode="."${fullPath#$magentoDir}

binDir=${DIR}/bin
meqpDir=${DIR}/meqp
reportDir=${DIR}/reports
phpStan=${binDir}/phpstan.phar
phpcs=${meqpDir}/vendor/bin/phpcs
phpcbf=${meqpDir}/vendor/bin/phpcbf

command -v patch >/dev/null 2>&1 || { echo "you need to have patch installed before running this"; exit 5; }

if [[ ! -d "${binDir}" ]]
then
    mkdir "${binDir}"
fi

if [[ ! -d "${reportDir}" ]]
then
    mkdir "${reportDir}"
fi

if [[ ! -f "${phpStan}" ]]
then
    echo "Getting the latest version of PHPStan"
    wget -q -O "${phpStan}" \
    `
        curl -s https://api.github.com/repos/phpstan/phpstan/releases/latest | \
        grep "download_url.*phpstan.phar\"" | \
        sed 's#.*"\(http[^"]*\)"#\1#'\
    `
fi

if [[ ! -d "${meqpDir}" ]]
then
    composer create-project --repository=https://repo.magento.com magento/marketplace-eqp ${meqpDir}
    ${phpcs} --config-set m2-path ${magentoDir}
fi

cd ${magentoDir}

echo "First fix what we can automatically"
${phpcbf} ${pathToCode} --standard=MEQP2 --ignore="Tests,Test,Fixtures"
echo "Now we run the Magento 2 code sniffer checks"
${phpcs} ${pathToCode} --standard=MEQP2 --ignore="Tests,Test,Fixtures"
echo "Now run PHPStan"
php "${phpStan}" analyse "${pathToCode}" -l7 -c "${DIR}/config/phpstan.neon"

cd ${magentoDir}/dev/tests/static
php ../../../vendor/bin/phpunit -c $PWD

#php "${magentoDir}/bin/magento" dev:tests:run static | tee "${reportDir}/magentoStatic.log"




echo "
----------------
$(hostname) $0 completed
----------------
"