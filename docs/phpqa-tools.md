## QA Tools

QA runs standard tools. At the moment this is:

- [PHP Code Beautifier and Fixer](../includes/generic/beautifierFixer.inc.bash)
- [PHP CodeSniffer](../includes/generic/codeSniffer.inc.bash)
- [Composer validation](../includes/generic/composerChecks.inc.bash)
- [Markdown link checker](../includes/generic/markdownLinks.inc.bash)
- [PHP Mess Detector](../includes/generic/messDetector.inc.bash)
- [PHP Lint](../includes/generic/phpLint.inc.bash)
- [PHP Lines of Code](../includes/generic/phploc.inc.bash)
- [PHPStan](../includes/generic/phpstan.inc.bash)
- [PHP7 Strict Types check](../includes/generic/phpStrictTypes.inc.bash)
- [PHPUnit](../includes/generic/phpunit.inc.bash)
- [PSR-4 validation](../includes/generic/psr4Validate.inc.bash)

There are also a couple of tools for the use of setting up PHPQA:

- [Prepare Directories for QA](../includes/generic/prepareDirectories.inc.bash)
- [Config setup](../includes/generic/setConfig.inc.bash)
- [Includes and Exclude paths](../includes/generic/setPaths.inc.bash)

These are run from the `bin/qa` script directly.

## Platform-specific tools