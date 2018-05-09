# QA Tools

QA runs a suite of standard tools, and are contained in the `includes/generic` folder. These are run from the `bin/qa` script via the `runTool` function.

At the moment this suite consists of:

- [PHP Code Beautifier and Fixer](../includes/generic/beautifierFixer.inc.bash): Automagically reformats PHP code according to defined coding standards
- [PHP CodeSniffer](../includes/generic/codeSniffer.inc.bash): Checks PHP code according to defined coding standards
- [Composer validation](../includes/generic/composerChecks.inc.bash): Runs a diagnose on composer to make sure it's all good
- [Markdown link checker](../includes/generic/markdownLinks.inc.bash): Starts at a root README.md file, and follows links looking for missing link destinations
- [PHP Mess Detector](../includes/generic/messDetector.inc.bash): Looks for messy code, such as unused variables, long functions and nest function calls. [PHPMD homepage](https://phpmd.org/)
- [PHP Lint](../includes/generic/phpLint.inc.bash): Very fast PHP linting process. Checks for syntax errors in your PHP files. [PHP Parallel Lint project page](https://github.com/JakubOnderka/PHP-Parallel-Lint)
- [PHP Lines of Code](../includes/generic/phploc.inc.bash): Statistics on lines of code
- [PHPStan](../includes/generic/phpstan.inc.bash): Static Analysis of your PHP code. [PHPStan project page](https://github.com/phpstan/phpstan)
- [PHP7 Strict Types check](../includes/generic/phpStrictTypes.inc.bash): Checks for files that do not have strict types defined and allows you to fix them
- [PHPUnit](../includes/generic/phpunit.inc.bash): PHP Unit testing. [Project page](https://github.com/phpstan/phpstan)
- [PSR-4 validation](../includes/generic/psr4Validate.inc.bash): Checks for code whose namespaces don't comply with the PSR-4 standard

There are also a few tools for the use of setting up PHPQA:

- [Prepare Directories for QA](../includes/generic/prepareDirectories.inc.bash)
- [Config setup](../includes/generic/setConfig.inc.bash)
- [Includes and Exclude paths](../includes/generic/setPaths.inc.bash)

## Platform-specific tools

The `runTool` function takes into account the platform that PHPQA detected via the `detectPlatform` function:

- It will first look inside `includes/{detectedPlatform}/{toolName}.inc.bash`
- If none is found, it will fall back to `includes/generic/{toolName}.inc.bash`

The platform-specific script will be run instead of the generic script. You can choose to `source` the generic tool in your script:

```
source $DIR/../includes/generic/setConfig.inc.bash
... platform-specific script contents ...
```

