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
readonly infectionVersion="0.9.0"
readonly infectionBinPath="./../bin/infection.phar"

#check and remove symlink
if [[ -L "$infectionBinPath" ]]
then
    rm "$infectionBinPath"
fi

#remove old versions
if [[ -e "$infectionBinPath" ]]
then
    currentVersion="$(./../bin/infection.phar -V --no-ansi | tail -n 1 | cut -d k -f 2 | xargs)"
    if [[ "$currentVersion" != "$infectionVersion" ]]
    then
        rm -f "$infectionBinPath" "$infectionBinPath.phar"
    fi
fi

#downloading it if it doesn't already exist
if [[ ! -e "$infectionBinPath" ]]
then
    wget "https://github.com/infection/infection/releases/download/$infectionVersion/infection.phar" \
        --output-document="$infectionBinPath"
    wget "https://github.com/infection/infection/releases/download/$infectionVersion/infection.phar.pubkey" \
        --output-document="$infectionBinPath.pubkey"

    chmod +x "$infectionBinPath"
fi

#performing a self update - this doesn't currently work unfortunately
#source ./../includes/functions.inc.bash
#phpBinPath="$(which php)"
#phpNoXdebug "$infectionBinPath" -- self-update

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
