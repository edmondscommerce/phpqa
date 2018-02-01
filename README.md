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

## Quick Tests

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

That isn't to say you shouldn't run them!

But it allows you to easily skip certain tests as part of this QA pipeline allowing faster iteration

You would then run your full test suite as normal occasoinally to ensure everything is working

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

### PHPStan
Static Analysis of your PHP code

#### Configuration 
Configuration file should be placed in `{project-root}/tests/phpstan.neon`

##### Boostrap
In the configuration you might want to specify a [php bootstrap file](https://github.com/phpstan/phpstan#bootstrap-file) to initialise your code

If you place you `phpstan-bootstrap.php` in `{project-root}/tests/phpstan-bootstrap.php`

Then neon file should look like:

```
parameters:
	bootstrap: %rootDir%/../../../../tests/phpstan-bootstrap.php
```


