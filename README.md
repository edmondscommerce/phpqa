# phpqa
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)
Simple PHP QA pipeline and scripts. Largely just a collection of dependencies with configuration and scripts to run them together

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/884a284be5cd4dd3a49c199119385f58)](https://www.codacy.com/app/edmondscommerce/phpqa?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/phpqa&amp;utm_campaign=Badge_Grade) [![Build Status](https://travis-ci.org/edmondscommerce/phpqa.svg?branch=master)](https://travis-ci.org/edmondscommerce/phpqa)

## Installing

```bash
composer require edmondscommerce/phpqa --dev
```

Your project's `composer.json` needs to specify a bin folder. If it's not already present, add this:

```
    ...
    "config": {
        "bin-dir": "bin"
    }
    ...
``` 

## Running

```bash
./bin/qa (optional/path/to/scan)
```

By default, PHPQA will run against the entire project root.

Adding a second parameter ensures that the tools scan only that folder.

### Environment Variables

These should be set in your terminal before running `bin/qa`, or can be set on a per-run basis with

```
(variableName)=(value) ./bin/qa
```

- **Quick tests only** `phpqaQuickTests`: If you want to run only fast PHPQA tests
- **CI Mode**: `CI`: performs actions headlessly without user prompts
- **Skip uncommitted check** `skipUncommittedChangesCheck`: don't check for uncommitted changes when running
- **Use Infection** `useInfection`: Set this to 0 to have PHPUnit run the unit tests instead of infection
- **Minimum MSI Percentage** `mutationScoreIndicator`: The minimum [MSI](https://infection.github.io/guide/#Mutation-Score-Indicator-MSI) required for PHPQA to pass
- **Minimum Covered MSI Percentage** `coveredCodeMSI`: The minimum [covered MSI](https://infection.github.io/guide/#Covered-Code-Mutation-Score-Indicator) level required for PHPQA to pass
 
## Configuration

Standard configuration is in the directory [./configDefaults/generic](./configDefaults/generic). If you have no need to override or extend these then you don't need to do anything.

If your project needs to have custom configuration, you'll need to copy the relevant config files into a new `qaConfig` folder in the root of your project.

See the [Configuration docs](./docs/configuration.md) for more details. 

## The Pipeline

Steps are run in sequence. Any step that fails kills the process.

In general the steps are in a logical progression of importance. So for example there is no point static analysing PHP code if the code syntax is not even valid.

Each tool is run in `bin/qa` using the `runTool` function.

[PHPQA's suite of tools](docs/phpqa-tools.md)

## Platform Detection

PHPQA comes with a set of generic tools and configs, but also has some inbuilt profiles.

Information on how this works can be found on the [platform detection page](docs/platform-detection.md)

Specific platforms' docs are at:

- [Magento 2](docs/magento2-overrides.md)

## Detailed Docs

### Hooks

If you would like to have some custom actions taken at the beginning of your qa process, after all configs are in place (and possibly overridden) but before any of the main checks take place then you can do this by creating a preHook.bash file in your `qaConfig` folder

This is an arbitrary bash script that can do anything you want.

It is sourced into the main qa process and so has access to all of the config variables as defined in the main script and overridden in your project config.

If you need something in this pre hook to fail the whole process, it is important to do the following:

```bash
# something in prehook needs to mark the process as failed...
exit 1;
```

In Bash - an exit code of greater than 0 indicates an error. You can pick any number you want, but 1 is the standard for "general error".

##### Bootstrap

In the configuration you might want to specify a [php bootstrap file](https://github.com/phpstan/phpstan#bootstrap-file) to initialise your code

If you place you `phpstan-bootstrap.php` in `{project-root}/tests/phpstan-bootstrap.php`

Then neon file should look like:

```
parameters:
	bootstrap: %rootDir%/../../../tests/phpstan-bootstrap.php

```

#### Strict Rules

The strict rules are brought in as a dependency and configured by default

To disable them, you need to setup your own config for PHPStan and remove this from your config file

```php
includes:
	- ../vendor/phpstan/phpstan-strict-rules/rules.neon

```
https://github.com/phpstan/phpstan-strict-rules

### Infection

[Infection](https://infection.github.io/) is a Mutation Testing Framework, that runs PHPUnit tests and then makes small 
modifications to the code and sees if these cause the unit tests to fail.

As this requires the unit tests to be run, we use this as the default test runner for PHP QA.

#### Configuration

You may need to tell infection where the configuration directory for PHPUnit is. To do this, override the
[./configDefaults/generic/infection.json](./configDefaults/generic/infection.json) file and add the following to it

```json
"phpUnit": {
    "configDir": "path/to/directory/with/phpunit.xml"
}
```

##### Setting Minimum Score Indicators

The easiest way to override the default minimum score indicators permanently for your project is to include these in a `qaConfig.inc.bash` file in your projects `qaConfig` folder.

You can see that this is being done in the phpqa project itself in it's own [qaConfig](./qaConfig) folder.

#### Minimum Mutation Score Indicators

Infection has been configured to require both a minimum MSI and covered MSI to be achieved for the test to pass.

Be default these are set to 60% for MSI, and 90% for covered MSI. These values can be overwritten by using environment 
variables. To do this simply export the following before running qa

 * `mutationScoreIndicator` to set the MSI level
 * `coveredCodeMSI` to set the covered MSI level

See the following page for more information on MSIs being used in CI [https://infection.github.io/guide/using-with-ci.html](https://infection.github.io/guide/using-with-ci.html)

#### Disabling Infection

If you would prefer to run PHPUnit instead of infection, export the `useInfection` variable as 0 to prevent it running

### PHPUnit

This step runs your PHPUnit tests

#### Quick Tests

There is an environment variable for PHPUnit set called `qaQuickTests`

Using this, you can allow your tests to take a different path, skip tests etc if they are long running. 

```php
<?php declare(strict_types=1);

use EdmondsCommerce\PHPQA\Constants;
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase {
    
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function setup(){
        if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            return;
        }
        //unnecessary setup stuff if not doing long running tests
    }
    
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */    
    public function testLongRunningThing(){
         if (isset($_SERVER[Constants::QA_QUICK_TESTS_KEY])
            && (int)$_SERVER[Constants::QA_QUICK_TESTS_KEY] === Constants::QA_QUICK_TESTS_ENABLED
        ) {
            $this->markTestSkipped('Quick tests is enabled');
        }
        //long running stuff
    }
}
```

That isn't to say you should not run them!

But it allows you to easily skip certain tests as part of this QA pipeline allowing faster iteration.

You would then run your full test suite as normal occasionally to ensure everything is working

#### Running With Full Tests

If you are using the Quicktests approach but would like to run the full pipeline with full tests, then you just need to do:

```bash
phpUnitQuickTests=0 bin/qa
```

And this will then run with full tests

#### Coverage

By default, the PHPUnit command will generate both textual output and HTML coverage.

The coverage report will go into the project root /var directory as configured in [./configDefaults/generic/phpunit.xml](./configDefaults/generic/phpunit.xml)

If you want to override the coverage report location, you will need to override this config file as normal.

You can disable the coverage report on the fly by doing:

```bash
phpUnitCoverage=0 bin/qa 
```

You might decide to do this if you are running these tests on travis, as you can see in [./travis.yml](./.travis.yml)

#### Persitantly Setting Coverage or Quick Tests

If in your development session you want to, for example, configure PHPUnit to run as quickly as possible, you might want to disable coverage persistantly in your shell session.


##### For Fastest Iterations

To have qa run as quickly as possible, you need to disable coverage

To do this you can simply 

```bash
export phpUnitCoverage=0
```

and then every time you run `./bin/qa` it will be as if you ran it like `phpUnitCoverage=0 ./bin/qa`

##### For Most Comprehensive Checking

For the most comprehensive checking, you need coverage enabled and also quick tests

```bash
export phpUnitQuickTests=0
```

and then every time you run `./bin/qa` it will be as if you ran it like `phpUnitQuickTests=0 ./bin/qa`

### PHP Mess Detector

https://phpmd.org/

We use all of the Mess Detector rules apart from the long variable name one.

If you want to suppress specific errors for certain methods etc, copy/paste some generic part of the message and then grep the `vendor/phpmd` directory, for example:

To find the rule for a message that contains `to keep number of public methods under'` 
```bash
grep 'to keep number of public methods under' -r vendor/phpmd/src/main/rulesets -B 4 | grep -P -o '(?<=rule name=")[^"]+'
```

To make life easy for yourself, you might make this a function in your bashrc file, or just paste it into your terminal session.

```bash

function phpmdRule(){
    local message="$@"
    local phpMdPath="$(find . \
        -wholename '*vendor/phpmd/phpmd/src/main/resources/rulesets*' \
        -type d 
    )"
    grep "$message" -r  -B 4 | grep -P -o '(?<=rule name=")[^"]+'
}

```

And then to use it, just:

```bash
phpmdRule public methods under
```

Whatever ruel is returned by phpmdRule call, supress it in PHP using dock block.


```php
    /**
     * @SuppressWarnings(PHPMD.SomeRule) 
     */
     publinc function someMethod() { }
```

### Markdown Links Checker

This is a small utility [bundled with this repo](./src/Markdown/LinksChecker.php)

It will check your `README.md` file and then recursively, all `*.md` files in the `docs` directory

What it does is check for broken internal links. This can happen all the time if you link through to other md files or link to specific code files or folders.

### Uncommitted Changes Check

At this point, the pipeline checks for uncommited changes in your repo. 

If there are uncommited changes then the process stops. This is because beyond the point, tools are used that will actively update the code. You need to be able to `git reset --hard HEAD` etc.

You can skip this step using `export skipUncommittedChangesCheck=1`

### PHP Code Beautifier and Fixer

Part of the PHP_CodeSniffer package

This will also fix any issues found

https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically

### PHP Code Sniffer

Now we run the code sniffer to check for any remaining coding standards issues that have not been automatically fixed.

The coding standard used defaults to PSR2

You can specify any standard you want thouhg.

https://github.com/squizlabs/PHP_CodeSniffer

#### Ignoring Parts of a File

You can mark specific chunks of code to be not analyzed by PHPCS having the following comments:

```php
<?php
$xmlPackage = new XMLPackage;
// phpcs:disable
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
// phpcs:enable

```

https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file


#### _TODO_ Include more coding standards and perform platform detection

Implement other coding standards. Ideally figure out some automation to select the correct coding standard based on the application code, for example 
* [magento/marketplace-eqp](https://github.com/magento/marketplace-eqp)
* [WordPress-Coding-Standards/WordPress-Coding-Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)




### PHPLOC

Simply enough, some statistics about your project

This is run last, it's not really a test, just for info

If you got here you made it!

## Running on Travis

You can use this pipeline on Travis-CI.

To see an example of how to do this, you can look at the [.travis.yml](./.travis.yml) and [./.travis.bash](./.travis.bash) files in this repo.

You can also look at [Doctrine Static Meta](https://github.com/edmondscommerce/doctrine-static-meta) as a more complete example - on travis [here](https://travis-ci.org/edmondscommerce/doctrine-static-meta).


### Post Hook

If you would like to have some custom actions taken at the end of your qa process once everythign is passed, then you can do this by creating a postHook.bash file in your `qaConfig` folder

This is an arbitrary bash script that can do anything you want.

It is sourced into the main qa process and so has access to all of the config variables as defined in te main script and overridden in your project config.


