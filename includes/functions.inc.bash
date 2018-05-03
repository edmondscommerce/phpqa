#!/usr/bin/env bash

readonly platformMagento2Project="magento2-project"
readonly platformMagento2Module="magento2-module"
readonly platformGeneric="generic"

function detectPlatform(){

    if [[ -f $projectRoot/$scanPath/etc/module.xml && -f $projectRoot/$scanPath/registration.php ]]
    then
        echo $platformMagento2Module
        return 0
    fi

    if [[ -f $projectRoot/$scanPath/bin/magento ]]
    then
        echo $platformMagento2Project
        return 0
    fi

    echo $platformGeneric
}

function runTool(){
    local tool="$1"
    local pathToTool="$DIR/../includes/$platform/$tool.inc.bash"
    if [[ -f $pathToTool ]]
    then
        echo "Running $platform $tool"
        source $pathToTool
        return 0
    fi
    pwd
        echo "Running generic $tool"
    source "$DIR/../includes/generic/$tool.inc.bash"
}

################################################################
# Get the path for a config file
# Config file will be search for in:
#   A qaConfig folder in the project root
#   The phpqa library's configDefaults/{platform}
#   The phpqa library's configDefaults/generic
#
# Usage:
#
# `configPath "relative/path/to/file/or/folder"
function configPath(){
    local relativePath="$1"
    local platformPath="$defaultConfigPath/$platform/$relativePath"
    local genericPath="$defaultConfigPath/generic/$relativePath"
    if [[ -f $projectConfigPath/$relativePath ]]
    then
        echo $projectConfigPath/$relativePath
    elif [[ -f $platformPath ]]
    then
        echo $platformPath
    else
        echo $genericPath
    fi
}

###############################################################
# Function to run PHP without Xdebug enabled, much faster
# Usage:
# `phpNoXdebug path/to/php/file.php -- -arg1 -arg2`
function phpNoXdebug {
    set +x
    local temporaryPath="$(mktemp -t php.XXXX).ini"
    # Using awk to ensure that files ending without newlines do not lead to configuration error
    $phpBinPath -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$temporaryPath"
    $phpBinPath -n -c "$temporaryPath" "$@"
    local exitCode=$?
    rm -f "$temporaryPath"
    set -x
    return $exitCode
}

###############################################################
# Function to check there are no uncommitted changes.
#
# This will also prompt you to commit these changes if you want to.
#
# Usage:
# checkForUncommitedChanges
function checkForUncommittedChanges {

    targetDir=${1:-$(pwd)}
    originalDir=$(pwd)

    if [[ ! -d $targetDir/.git/ ]]
    then
        echo "$targetDir is not a git repo"
        return
    fi

    cd $targetDir

    if [[ "true" == "$CI" ]]
    then
        echo "Skipping uncommited changes check in CI"
        return 0;
    fi
    set +e
    inGitRepo="$(git rev-parse --is-inside-work-tree 2>/dev/null)"
    if [[ "" != "$inGitRepo" ]]
    then
        git status | grep -Eq "nothing to commit, working .*? clean"
        repoDirty=$?
        set -e
        if (( $repoDirty > 0 ))
        then
            git status
            echo "

    ==================================================

        Untracked or Uncommited changes detected

        Would you like to commit (c) or abort (a)

        (git commit will be 'git add -A; git commit')

    ==================================================

            "
            read -n 1 commitOrAbort
            if [[ "$commitOrAbort" != "c" ]]
            then
                printf "\n\n\nAborting...\n\n\n"
                exit 1
            fi
            git add -A
            git commit
        fi
    fi

    cd $originalDir
}

function tryAgainOrAbort(){
    toolname="$1"
    echo "

    ==================================================

        $toolname Failed...

        would you like to try again? (y/n)

    ==================================================

    "
    read -n 1 tryAgainOrAbort
    if [[ "y" != "$tryAgainOrAbort" ]]
    then
        printf "\n\nAborting...\n\n"
        exit 1
    fi
    printf "\n\nTrying again, good luck!\n\n"
    hasBeenRestarted="true"
}

function findTestsDir(){
    testsDir="$(find $projectRoot -maxdepth 1 -type d \( -name test -o -name tests \) | head -n1)"
    if [[ "" == "$testsDir" ]]
    then
        echo "


    ##### ERROR ############################################


    You have no 'tests' or 'test' directory.

    This is not currently supported by phpqa

    Please create at least an empty 'tests' directory, eg:

    mkdir -p $projectRoot/tests


    ########################################################

        " 1>&2;
        exit 1
    fi
    echo "$testsDir"
}


function findSrcDir(){
    srcDir="$projectRoot/src"
    if [[ "" == "$srcDir" ]]
    then
        echo "


    ##### ERROR ############################################


    You have no 'src' or directory.

    This is not currently supported by phpqa

    Please create at least an empty 'src' directory, eg:

    mkdir -p $projectRoot/src


    ########################################################

        " 1>&2;
        exit 1
    fi
    echo "$srcDir"
}

function findBinDir(){
    binDir="$(find $projectRoot -maxdepth 2 -type d -name bin | head -n1)"
    if [[ "" == "$binDir" ]]
    then
        echo "


    ##### ERROR ############################################


    You have no 'bin' or directory.

    This is not currently supported by phpqa

    Please create at least an empty 'bin' directory, eg:

    mkdir -p $projectRoot/bin


    ########################################################

        " 1>&2;
        exit 1
    fi
    echo "$binDir"
}
