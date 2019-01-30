#!/usr/bin/env bash

readonly platformMagento2="magento2"
readonly platformGeneric="generic"
readonly platformLaravel="laravellumen"

function detectPlatform(){

    if [[ -f $projectRoot/$specifiedPath/etc/module.xml && -f $projectRoot/$specifiedPath/registration.php ]]
    then
        echo $platformMagento2
        return 0
    fi

    if [[ -f $projectRoot/bin/magento ]]
    then
        echo $platformMagento2
        return 0
    fi

    if [[ -f $projectRoot/artisan ]]
    then
        echo $platformLaravel;
        return 0;
    fi

    echo $platformGeneric
}

################################################################
# Run a tool
# First check for a project qaConfig tool override
# Then a platform tool
# finally the Generic tool
function runTool(){
    local tool="$1"
    local projectOverridePath="$projectConfigPath/tools/$tool.inc.bash"
    local platformPath="$DIR/../includes/$platform/$tool.inc.bash"
    local genericPath="$DIR/../includes/generic/$tool.inc.bash"

    if [[ -f "$projectOverridePath" ]]
    then
        echo "Running Project Override $tool"
        source "$projectOverridePath"
        return 0
    elif [[ -f "$platformPath" ]]
    then
        echo "Running $platform $tool"
        source "$platformPath"
        return 0
    fi
    echo "Running generic $tool"
    source "$genericPath"
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
    if [[ ! -f ${noXdebugConfigPath} ]]
    then
        # Using awk to ensure that files ending without newlines do not lead to configuration error
        ${phpBinPath} -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$noXdebugConfigPath"
    fi
    set -x
    ${phpBinPath} -n -c "$noXdebugConfigPath" "$@"
    set +x
    echo
    local exitCode=$?
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
    if [[ "false" != "${CI:-'false'}" ]]
    then
        echo "Skipping uncommited changes check in CI"
        return 0;
    fi
    if [[ "$skipUncommittedChangesCheck" == "1" ]]
    then
        echo "Skipping uncommitted changes check. export skipUncommittedChangesCheck=0 to reinstate"
        return 0
    fi

    targetDir=${1:-$(pwd)}
    originalDir=$(pwd)

    if [[ ! -d $targetDir/.git/ ]]
    then
        echo "$targetDir is not a git repo"
        return
    fi

    cd $targetDir

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

        or export skipUncommittedChangesCheck=1 to ignore

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

function phpunitReRunFailedOrFull(){
    if [[ "false" != "${CI:-'false'}" ]]
    then
        return 0;
    fi
    local rerunFailed
    local reunLogFileTimeLimit=${phpunitRerunTimeoutMins:-5}
    local rerunLogFile="$(find $varDir -type f -name 'phpunit.junit.xml' -mmin -$reunLogFileTimeLimit)";
    if [[ "" == "$rerunLogFile" ]]
    then
        echo "";
        return 1;
    fi
    echo "

    ==================================================

        PHPUnit Run detected from less than $reunLogFileTimeLimit mins ago

        Would you like to just rerun failed tests?

        (will timeout and run full in 10 seconds)

    ==================================================

        "
    set +e
    read  -t 10 -n 1 rerunFailed
    set -e
    if [[ "y" != "$rerunFailed" ]]
    then
        printf "\n\nRunning Full...\n\n"
        return 1
    fi
    printf "\n\nRerunning Failed Only\n\n"
    return 0;
}


function tryAgainOrAbort(){
    toolname="$1"
    if [[ "false" != "${CI:-'false'}" ]]
    then
        echo "

    ==================================================

        $toolname Failed...

    ==================================================

        "
        exit 1
    fi
    echo "

    ==================================================

        $toolname Failed...

        would you like to try again? (y/n)

        (note: if you change config files, you might have to run from the top for it to take effect...)

    ==================================================

    "
    while read -n 1 tryAgainOrAbort
    do
        if [[ "n" == "$tryAgainOrAbort" ]]
        then
            printf "\n\nAborting...\n\n"
            exit 1
        fi
        if [[ "y" == "$tryAgainOrAbort" ]]
        then
            break;
        fi
        printf "\n\ninvalid choice: $tryAgainOrAbort - should be y or n \n\n        would you like to try again? (y/n)"
    done
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
