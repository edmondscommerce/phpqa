echo "

Validating PSR-4 Roots
------------------------
"

runTool psr4Validate

echo "

Checking for Composer Issues
----------------------------
"

runTool composerChecks

echo "
Setting Strict Types If It's Missing
-------------------------------------
"

runTool phpStrictTypes

echo "

Running PHP Lint
----------------
"
runTool phpLint

echo "

Running PHPUnit Annotations Check
--------------------------------
"
runTool phpunitAnnotations

echo "

Running Composer Require Checker
--------------------------------
"
runTool composerRequireChecker


echo "

Running Markdown Links Checker
------------------------------
"
runTool markdownLinks
