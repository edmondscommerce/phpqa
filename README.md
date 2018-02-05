# phpqa
Simple PHP QA pipeline and scripts. Largely just a collection of dependencies with configuration and scripts to run them together


## Installing

```bash
composer require edmondscommerce/phpqa --dev
```

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


### M2 Configuration

Working on the assumption that this tool has been installed in the mage root folder, there are some pre-defined
config paths in the qaConfig directory

You can use these by running the following commands

```bash

cd /path/to/qa/tool/qaConfig

cp m2Config.inc.bash qaConfig.inc.bash
cp m2-phpstan.neon phpstan.neon

```

And then making any changes as required by your setup

## Tools

Tools run in sequence. Any tool that fails kills the process

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

Default configuration is in [](./configDefaults/phpstan.neon)

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

The strict rules are brought in as a dependency

To enable them, you need to add this to your config file

```php
includes:
	- vendor/phpstan/phpstan-strict-rules/rules.neon

```
https://github.com/phpstan/phpstan-strict-rules

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

### Uncommitted Changes Check

At this point, the pipeline checks for uncommited changes in your repo. 

If there are uncommited changes then the process stops. This is because beyond the point, tools are used that will actively update the code. You need to be able to `git reset --hard HEAD` etc.

### Coding Standard Fixer

This tool will actively update your code to fix coding standards issues

https://github.com/FriendsOfPHP/PHP-CS-Fixer

### PHP Code Beautifier and Fixer

Part of the PHP_CodeSniffer package

This will also fix any issues found

https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically

### PHP Code Sniffer

Now we run the code sniffer to check for any remaining coding standards issues that have not been automatically fixed.

The coding standard used defaults to PSR2

You can specify any standard you want thouhg.

https://github.com/squizlabs/PHP_CodeSniffer


#### _TODO_ Include more coding standards and perform platform detection

Implement other coding standards. Ideally figure out some automation to select the correct coding standard based on the application code, for example 
* [magento/marketplace-eqp](https://github.com/magento/marketplace-eqp)
* [WordPress-Coding-Standards/WordPress-Coding-Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)

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




### PHPLOC

Simply enough, some statistics about your project

This is run last, it's not really a test, just for info

