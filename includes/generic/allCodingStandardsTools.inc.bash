echo "

Running PHP-CS-Fixer
----------------------------
"

runTool phpCsFixer

# Code Sniffer does not currently support PHP8, for example
# https://github.com/squizlabs/PHP_CodeSniffer/issues/3167
# Hopefully 3.6 release will fix this, but for now lets disable the whole thing because it's totally broken
#echo "
#
#Running Beautifier and Fixer
#----------------------------
#"
#
#runTool beautifierFixer
#
#echo "
#
#Running Code Sniffer
#--------------------
#"
#
#runTool codeSniffer
