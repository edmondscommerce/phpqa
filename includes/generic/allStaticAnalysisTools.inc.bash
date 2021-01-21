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
echo " --- MESS DETECTOR SKIPPED - CURRENTLY BROKEN IN PHP8 ---"
#runTool messDetector


