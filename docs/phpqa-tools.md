# QA Tools

QA runs a suite of standard tools, and are contained in the `includes/generic` folder. These are run from the `bin/qa` script via the `runTool` function.

At the moment this suite consists of:

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

