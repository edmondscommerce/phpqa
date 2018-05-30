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

To disable them, you need to setup your own config for PHPStan and remove this from your config file

```php
includes:
	- ../vendor/phpstan/phpstan-strict-rules/rules.neon

```
see [the main PHPSTan docs](https://github.com/phpstan/phpstan-strict-rules) for more information

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
