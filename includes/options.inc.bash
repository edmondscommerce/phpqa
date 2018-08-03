#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'

singleToolToRun=
specifiedPath=

function usage {
    echo "Usage:"
    echo "bin/qa [-t tool to run ] [ -p path to scan ]"
    echo ""
    echo "Defaults to using all tools and scanning whole project based on platform"
    echo ""
    echo " - use -h to see this help"
    echo ""
    echo " - use -p to specify a specific path to scan"
    echo ""
    echo " - use -t to specify a single tool:"
    echo "     psr|psr4                   psr4 validation"
    echo "     com|composer               composer validation"
    echo "     st|stricttypes             strict types validation"
    echo "     lint|phplint               phplint"
    echo "     stan|phpstan               phpstan"
    echo "     ann|phpunitAnnotations     phpunitAnnotations"
    echo "     unit|phpunit               phpunit"
    echo "     uniterate                  phpunit iterative mode - prioritise broken tests and fail on error"
    echo "     infect|infection           infection"
    echo "     md|messdetector            php mess detector"
    echo "     ml|markdown                markdown validation"
    echo "     bf|phpbf                   php beautifier and fixer"
    echo "     cs|phpcs                   php code sniffer"
    echo "     l|loc                      lines of code and other stats"
    exit 1
}

while getopts ":t:p:h" opt; do
    case "$opt" in
        t) singleToolToRun=$OPTARG ;;
        p) specifiedPath=$OPTARG ;;
        h) usage ;;
        \?)
            printf "\nERROR:\nInvalid option: -$OPTARG\n\n" >&2
            usage
        ;;
        :)
            printf "\nERROR\nOption -$OPTARG requires an argument\n\n" >&2
            usage
        ;;
    esac
done

if [[ "" != "$singleToolToRun" ]]
then
    case "$singleToolToRun" in
        psr | psr4                  ) singleToolToRun="psr4Validate";;
        com | composer              ) singleToolToRun="composerChecks";;
        st | stricttypes            ) singleToolToRun="phpStrictTypes";;
        lint | phplint              ) singleToolToRun="phpLint";;
        stan | phpstan              ) singleToolToRun="phpstan";;
        ann | phpunitAnnotations    ) singleToolToRun="phpunitAnnotations";;
        unit | phpunit              ) singleToolToRun="phpunit";;
        uniterate                   ) singleToolToRun="phpunit"; phpUnitIterativeMode=1;;
        infect | infection          ) singleToolToRun="infection";;
        md | messdetector           ) singleToolToRun="messDetector";;
        ml | markdown               ) singleToolToRun="markdownLinks";;
        bf | phpbf                  ) singleToolToRun="beautifierFixer";;
        cs | phpcs                  ) singleToolToRun="codeSniffer";;
        l | loc                     ) singleToolToRun="phploc";;
        * )
            printf "\nERROR:\nInvalid tool: $singleToolToRun\n\n" >&2
            usage
        ;;
    esac
    echo "Running Single Tool: $singleToolToRun"
fi

if [[ "" != "$specifiedPath" ]]
then
    echo "Scanning Specified Path: $specifiedPath"
fi
