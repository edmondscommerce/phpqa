# phpqa
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)
Simple PHP QA pipeline and scripts. Largely just a collection of dependencies with configuration and scripts to run them together

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/884a284be5cd4dd3a49c199119385f58)](https://www.codacy.com/app/edmondscommerce/phpqa?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/phpqa&amp;utm_campaign=Badge_Grade) [![Build Status](https://travis-ci.org/edmondscommerce/phpqa.svg?branch=master)](https://travis-ci.org/edmondscommerce/phpqa)

## Installing

```bash
composer require edmondscommerce/phpqa --dev
```

## Installing Extra Coding Standards

Have a look at [Dealerdirect/phpcodesniffer-composer-installer/](https://github.com/Dealerdirect/phpcodesniffer-composer-installer/) for a nice way to easily add extra coding standards to code sniffer

## Running

```bash
./bin/qa
```

If you want to run all PHPUnit tests, you need to do this:

```bash
phpUnitQuickTests=0 ./bin/qa
```

## Configuration

Standard configuration is in the directory [./configDefaults](./configDefaults)

If your project needs to have custom configuration, you have 2 ways of doing this:

### Single Tool Configuration Override

For each tool, you can override the configuration by:

1. Make a directory in your project root called `qaConfig`
2. Copy the configuration from ./configDefaults](./configDefaults) into your project `qaConfig` folder.
3. Customise the copied config as you see fit

For example, for PHPStan, we would do this:

```bash
#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy in the default config
cp vendor/edmondscommerce/phpqa/configDefaults/phpstan.neon qaConfig

#Edit the config
vim qaConfig/phpstan.neon
```

For PHP Mess Detector, we would do this:

```bash
#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy the phpmd folder as a whole
cp vendor/edmondscommerce/phpqa/configDefaults/phpmd/ qaConfig/ -r


```

### Global Configuration Override

If you want to make more wholesale tweaks to `qa` customisation, you can do this:

```bash

#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy in the default config
awk '/#### CONFIG /,/#### PROCESS /' bin/qa > qaConfig/qaConfig.inc.bash

#Edit the config
vim qaConfig/qaConfig.inc.bash

```

When you run `qa`, it checks for a file located in `"$projectConfigPath/qaConfig.inc.bash"` and will include it if found. 

Using this you can override as much of the standard configuration as you see fit.

## The Pipeline

Steps are run in sequence. Any step that fails kills the process.

In general the steps are in a logical progression of importance. So for example there is no point static analyzing PHP code if the code syntax is not even valid.

Below is a description of each step in order.

### Pre Hook

If you would like to have some custom actions taken at the beginning of your qa process, after all configs are in placed (and possibly overridden) but before any of the main checks take place then you can do this by creating a preHook.bash file in your `qaConfig` folder

This is an arbitrary bash script that can do anything you want.

It is sourced into the main qa process and so has access to all of the config variables as defined in te main script and overridden in your project config.

If you need something in this pre hook to fail the whole process, it is important that do the following:

```bash

# something in prehook needs to mark the process as failed...
exit 1;
```

In Bash - an exit code of greater than 0 indicates an error. You can pick any number you want, but 1 is the standard for "general error".

### PSR4 Validator
This enforces that PSR4 guidlines are being adhered to correctly.

#### Ignore List
You can specify a number of files or directories to be ignored by the validator. This is a newline seperated
list of valid regex including a valid regex delimiter. For example:

```
#tests/bootstrap\.php#
#tests/phpstan-bootstrap\.php#
```

### Composer Check For Issues
Runs a diagnose on composer to make sure it's all good

### Dump Autoloader
To ensure your latest files are properly included

### Strict Types Enforcing
Checks for files that do not have strict types defined and allows you to fix them

### PHP Parallel Lint
Very fast PHP linting process. Checks for syntax errors in your PHP files

https://github.com/JakubOnderka/PHP-Parallel-Lint

### PHPStan
Static Analysis of your PHP code

https://github.com/phpstan/phpstan

#### Configuration 

Default configuration is in [./configDefaults/phpstan.neon](./configDefaults/phpstan.neon)

To override the configuration you need to copy it to `{project-root}/qaConfig/phpstan.neon`

For specifying paths, just know the root of the project is `%rootDir%/../../../`

##### Boostrap

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

#### Supressing Errors

[Here](https://github.com/phpstan/phpstan#ignore-error-messages-with-regular-expressions) you can read more about, how to
ignore errors by modifying `phpstan.neon` file.

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

If enabled, the PHPUnit command will generate both textual output and HTML coverage.

The coverage report will go into the project root /var directory as configured in [./configDefaults/phpunit-with-coverage.xml](./configDefaults/phpunit-with-coverage.xml)

If you want to override the coverage report location, you will need to override this config file as normal.

You can enable the coverage report on the fly by doing:

```bash
phpUnitCoverage=1 bin/qa 
```

You might decide to do this if you are running these tests on travis, as you can see in [./travis.yml](./.travis.yml)

#### Persitantly Setting Coverage or Quick Tests

If in your development session you want to, for example, configure PHPUnit to run as quickly as possible, you might want to disable coverage persistantly in your shell session.


##### For Fastest Iterations

To have qa run as quickly as possible, you need to disable coverage

To do this you can simply 

```bash
export phpUnitQuickTests=1
```

and then every time you run `./bin/qa` it will be as if you ran it like `phpUnitCoverage=0 ./bin/qa`

##### For Most Comprehensive Checking

For the most comprehensive checking, you need coverage enabled and also quick tests

```bash
export phpUnitQuickTests=0
export phpUnitCoverage=1
```

and then every time you run `./bin/qa` it will be as if you ran it like `phpUnitQuickTests=0 ./bin/qa`

#### Paratest

You can run multiple sets of PHPUnit tests in parallel using [paratest](https://github.com/paratestphp/paratest)

Currently this is experimental and will certainly not work in a variety of situations.

To enable paratest, simply install it. If it is found, this QA process will use it.

```bash
composer require --dev brianium/paratest
```

##### Further Reading

* https://github.com/brianium/paratest-selenium

#### PHPUnit and PHPStan

We also include [https://github.com/phpstan/phpstan-phpunit](https://github.com/phpstan/phpstan-phpunit) which allows you to properly use mocks with PHPUnit tests and keep PHPStan happy.

see [https://github.com/phpstan/phpstan-phpunit#how-to-document-mock-objects-in-phpdocs](https://github.com/phpstan/phpstan-phpunit#how-to-document-mock-objects-in-phpdocs) for full instructions on how to document mock objects in your tests.

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

Whatever rule is returned by phpmdRule call, supress it in PHP using dock block.


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

### Coding Standards

We use the PHP_CodeSniffer package to handle coding standards, including automatically fixing where possible.

The coding standard is defaulted to PSR2 but can be easily overridden by creating a folder (or symlink) in your project `qaConfig` folder called `codingStandards`

This allows you to use your own custom coding standard, a third party standard or one of the built in CodeSniffer standards.

For example, to use Zend you could run:

```bash
cd $projectRoot
mkdir -p qaConfig
cd qaConfig
ln -s ../vendor/squizlabs/php_codesniffer/src/Standards/Zend codingStandards
```

If you wanted to use the Symfony coding standard, you can do:

```bash
cd $projectRoot
composer require escapestudios/symfony2-coding-standard
mkdir -p qaConfig
cd qaConfig
ln -s ../vendor/escapestudios/symfony2-coding-standard/Symfony codingStandards
```


#### PHP Code Beautifier and Fixer

Part of the PHP_CodeSniffer package

This will also fix any issues found

https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically

#### PHP Code Sniffer

Now we run the code sniffer to check for any remaining coding standards issues that have not been automatically fixed.

https://github.com/squizlabs/PHP_CodeSniffer

##### Ignoring Parts of a File

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


