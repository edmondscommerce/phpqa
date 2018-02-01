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
