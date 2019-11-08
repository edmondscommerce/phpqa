echo "

Running PHPStan
---------------------
"
if [[ "$phpqaQuickTests" == "1" ]]
then
    echo "Skipping PHPStan because \$phpqaQuickTests=1"
else
    runTool phpstan
fi

echo "

Running PHP Mess Detector
-------------------------
"
runTool messDetector

echo "

Running Beautifier and Fixer
----------------------------
"

runTool beautifierFixer

echo "

Running Code Sniffer
--------------------
"

runTool codeSniffer
