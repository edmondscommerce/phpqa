# PHPQA PHPUnit

Here is the full documentation about how you can use and configure PHPUnit in your PHPQA project.

## Quick Tests

There is an environment variable for PHPUnit set called `phpUnitQuickTests`

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

Generally in CI you would always run your full suite of tests, but in local development you might decide to enable the quick tests mode.

### Running With Full Tests

If you are using the Quicktests approach but would like to run the full pipeline with full tests, then you just need to do:

```bash
phpUnitQuickTests=0 bin/qa
```

And this will then run with full tests

## Coverage

If enabled, the PHPUnit command will generate both textual output and HTML coverage.

The coverage report will go into the project root /var directory as configured in [./configDefaults/phpunit-with-coverage.xml](./configDefaults/phpunit-with-coverage.xml)

If you want to override the coverage report location, you will need to override this config file as normal.

You can enable the coverage report on the fly by doing:

```bash
phpUnitCoverage=1 bin/qa 
```

You might decide to do this if you are running these tests on travis, as you can see in [./travis.yml](./.travis.yml)

### Speed Impact of Enabling Coverage

If coverage is enabled, then the tests have to be run with Xdebug enabled. This on it's own has a dramatic impact on the speed of PHP execution.

This is in addition to the time required to actually generate and write the coverage reports. For a large test suite the time impact can be significant.

### Persitantly Setting Coverage or Quick Tests

If in your development session you want to, for example, configure PHPUnit to run as quickly as possible, you might want to disable coverage persistently in your shell session.

#### For Fastest Iterations

To have qa run as quickly as possible, you need to disable coverage

To do this you can simply 

```bash
export phpUnitQuickTests=1
```

and then every time you run `./bin/qa` it will be as if you ran it like `phpUnitCoverage=0 ./bin/qa`

#### For Most Comprehensive Checking

For the most comprehensive checking, you need coverage enabled and also quick tests

```bash
export phpUnitQuickTests=0
export phpUnitCoverage=1
```

and then every time you run `./bin/qa` it will be as if you ran it like `phpUnitQuickTests=0 ./bin/qa`

## Paratest

You can run multiple sets of PHPUnit tests in parallel using [paratest](https://github.com/paratestphp/paratest)

Currently this is experimental and will certainly not work in a variety of situations.

To enable paratest, simply install it. If it is found, this QA process will use it.

```bash
composer require --dev brianium/paratest
```

### Further Reading

* https://github.com/brianium/paratest-selenium

## PHPUnit and PHPStan

We suggest that you install [https://github.com/phpstan/phpstan-phpunit](https://github.com/phpstan/phpstan-phpunit) which allows you to properly use mocks with PHPUnit tests and keep PHPStan happy.

Read the [PHPQA PHPStan docs](./phpstan.md) for more information on this.


## Rerun Failed Tests

As with the other tools, there is an option to rerun this step if it fails.

Where PHPUnit is different is that you also get the option to only rerun your failed tests.

This uses another bin command [./bin/phpunit-runfailed-filter](./bin/phpunit-runfailed-filter) which generates the filter syntax to pull out the failed tests.

You can also use this in isolation if you want, eg:

```bash
./bin/phpunit -c qaConfig/phpunit.xml --filter "$(bin/phpunit-runfailed-filter)" tests/
```

## Infection

Another tool that runs your PHPUnit tests is Infection. This will only run if Xdebug is enabled and you have configured PHPUnit to generate coverage.
