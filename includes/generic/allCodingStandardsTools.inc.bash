echo "

Running PHP-CS-Fixer
----------------------------
"

runTool phpCsFixer

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
