# PHPQA PHPStan

Here are the full details of how PHPStan is used with PHPQA and how you can configure it for your projects

#### Configuration 

Default configuration is in [./configDefaults/generic/phpstan.neon](./../../configDefaults/generic/phpstan.neon)

To override the configuration you need to copy it to `{project-root}/qaConfig/phpstan.neon`

Specifying paths can be a little bit tricky, you can have a look at the [qaConfig/phpstan.neon](./../../qaConfig/phpstan.neon) override file for the PHPQA project itself for an example.

##### Extending Default Config

You can use the standard config as a base by using a template like:

```neon
includes:
    - ../vendor/edmondscommerce/phpqa/configDefault/phpstan.neon
```

##### Boostrap

In the configuration you might want to specify a [php bootstrap file](https://github.com/phpstan/phpstan#bootstrap-file) to initialise your code

If you place you `phpstan-bootstrap.php` in `{project-root}/tests/phpstan-bootstrap.php`

Then neon file should look like:

```
parameters:
	bootstrap: ../tests/phpstan-bootstrap.php

```

#### Strict Rules

The strict rules are brought in as a dependency and configured by default

PHPQA now uses the PHPStan extension loader - at the time of writing it is not clear how to disable auto loaded extensions.
Instead (for now) you will need to ignore specific rule failures instead of trying to disable the strict rule set.

See [the main PHPSTan docs](https://github.com/phpstan/phpstan-strict-rules) for more information

#### Supressing Errors

[Here](https://github.com/phpstan/phpstan#ignore-error-messages-with-regular-expressions) you can read more about, how to
ignore errors by modifying `phpstan.neon` file.

#### Mock Objects in Tests

Default PHPStan gets confused by mock objects:

```text
 ------ ------------------------------------------------------------------------------------------ 
  Line   Path/To/Class.php                                          
 ------ ------------------------------------------------------------------------------------------ 
  20     Parameter #1 $logger of class Path\To\AnotherClass constructor expects  
         Psr\Log\LoggerInterface, PHPUnit\Framework\MockObject\MockObject given.                   
 ------ ------------------------------------------------------------------------------------------ 
```

To solve this for PHPUnit mock objects you can use PHPStan's official PHPUnit extension
[phpstan-phpunit](https://github.com/phpstan/phpstan-phpunit).

You can find clear instructions on how to use this here [projects Github page](https://github.com/phpstan/phpstan-phpunit#usage).

Also read: [https://github.com/phpstan/phpstan-phpunit#how-to-document-mock-objects-in-phpdocs](https://github.com/phpstan/phpstan-phpunit#how-to-document-mock-objects-in-phpdocs) for full instructions on how to document mock objects in your tests.

##### Installing

**_NOTE: phpstan-phpunit will only work with PHP 7.1 and above._** which is why we don't bundle it by default.

First you'll need to require phpstan-phpunit:

```bash
composer require --dev phpstan/phpstan-phpunit
```

Then you need to copy the default PHPStan config from
`vendor/edmondscommerce/phpqa/configDefaults/generic/phpstan.neon`
to `qaConfig/phpstan.neon` and uncomment the 2 PHPUnit specific lines:

```bash
    - ../vendor/phpstan/phpstan-phpunit/extension.neon
    - ../vendor/phpstan/phpstan-phpunit/rules.neon
```

### Tips for Resolving Issues

#### Can't use `empty()`

You should not use empty, instead you should do more typesafe comparisons.

For example:

```php
<?php
$maybeEmptyArray=getMaybeEmptyArray();
if([]===$maybeEmptyArray){
    throw new \RuntimeException('the array is empty');
}
```

#### Type Can Be False or Otherwise not certain

You need to be more explicit about the type you are dealing with. For example, you can safely cast false to empty string if that is suitable in your situation. If not, then you should check for false and handle that as an Exception.

For example:

```php
<?php
$contents=\file_get_contents('/path/to/file');
if(false === $contents){
    throw new \RuntimeException('Failed getting file contents');
}
#now work with $contents as a string
```

See [\EdmondsCommerce\PHPQA\Psr4Validator::getActualNamespace](./../../src/Psr4Validator.php)

#### Only Booleans allowed in `if` Conditions

This means you need to do something explicitly boolean, generally involving a `===`

For example

```php
<?php
$subject='string containing pattern';
if(1===\preg_match('%pa[t]{2}ern%', $subject)){
    echo 'it matches';
}
```

#### PHPUnit Dynamic Call to Static Method

The convention is often to use `$this->assertSame`

Actually, the `assertSame` method is static, so you should really be doing `self::assertSame`

Generally you should be able to fix this in bulk by doing a "Replace in path" finding `$this->assert` and replacing with `self::assert`

#### Missing Type Hints

To resolve this, you should first try to declare a real PHP type hint

If you can't, for example the type is mixed, or you are extending or overriding a third party or core class, then you can still declare type hints but just using the legacy docblock method

For example:

Have a look at [\EdmondsCommerce\PHPQA\Psr4Validator::getDirectoryIterator](./../../src/Psr4Validator.php)

```php
<?php

#....

    /**
     * @param string $realPath
     *
     * @return \SplHeap|\SplFileInfo[]
     */
    private function getDirectoryIterator(string $realPath)
    {
        $directoryIterator = new \RecursiveDirectoryIterator(
            $realPath,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iterator          = new \RecursiveIteratorIterator(
            $directoryIterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        return new class($iterator) extends \SplHeap
        {
            public function __construct(\RecursiveIteratorIterator $iterator)
            {
                foreach ($iterator as $item) {
                    $this->insert($item);
                }
            }

            /**
             * @param \SplFileInfo $item1
             * @param \SplFileInfo $item2
             *
             * @return int
             */
            protected function compare($item1, $item2): int
            {
                return strcmp($item2->getRealPath(), $item1->getRealPath());
            }
        };
    }


```
