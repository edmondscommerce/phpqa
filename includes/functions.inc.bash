#!/usr/bin/env bash
################################################################
# Get the path for a config file
# Defaults to project level and falls back to this library
#
# Usage:
#
# - relative path, falling back to the standard default config path
# `configPath "relative/path/to/file/or/folder"`
#
# - relative path, falling back to a specified default
# `configPath "relative/path/to/file/or/folder" "specified/default/path"
function configPath(){
    local relativePath="$1"
    local defaultPath="${2:-"$defaultConfigPath/$relativePath"}"
    if [[ -f $projectConfigPath/$relativePath ]]
    then
        echo $projectConfigPath/$relativePath
    else
        echo $defaultPath
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
}

function phpunitRunFailed(){
    local rerunLogFile="$(find $varDir -type f -name 'phpunit.junit.log.xml' -mtime -5)";
    if [[ "" == "$rerunLogFile" ]]
    then
        echo "";
        return 0;
    fi
    echo phpNoXdebug bin/

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
