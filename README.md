# phpqa
Simple PHP QA pipeline and scripts. Largely just a collection of dependencies with configuration and scripts to run them together


## Installing

```bash
composer require edmondscommerce/phpqa --dev
```

## Running

```bash
./bin/phpqa
```



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

Configuration file should be placed in `{project-root}/tests/phpstan.neon`

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

There is an environment variable for PHPUnit set called `quickTests`

Using this, you can allow your tests to take a different path, skip tests etc if they are long running. 

```php
<?php
class MyTest extends TestCase {
    
    public function testLongRunningThing(){
        if(isset($_SERVER['quickTests']) && $_SERVER['quickTests'] == 1){
            $this->markTestAsSkipped();
        }
        //long running stuff
    }
}
```

That isn't to say you should not run them!

But it allows you to easily skip certain tests as part of this QA pipeline allowing faster iteration.

You would then run your full test suite as normal occasionally to ensure everything is working



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

Currently this is hard coded to use:
`$projectRoot/vendor/escapestudios/symfony2-coding-standard/Symfony`
https://github.com/djoos/Symfony-coding-standard

#### _TODO_

Implement other coding standards. Ideally figure out some automation to select the correct coding standard based on the application code, for example 
* [magento/marketplace-eqp](https://github.com/magento/marketplace-eqp)
* [WordPress-Coding-Standards/WordPress-Coding-Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)

### PHP Mess Detector


https://phpmd.org/


https://github.com/squizlabs/PHP_CodeSniffer


### PHPLOC

Simply enough, some statistics about your project

This is run last, it's not really a test, just for info

