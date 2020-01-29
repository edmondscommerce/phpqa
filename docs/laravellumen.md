# PHPQA Laravel and Lumen

PHPQA will detect both Laravel and Lumen as part of the detect platform stage.

Laravel and Lumen specific configuration resides in [configDefaults/laravellumen](./../configDefaults/laravellumen)

Laravel and Lumen specific tools reside in [includes/laravellumen](./../includes/laravellumen)

The customisations are very light and generally the generic configuration and tools are used, the main difference 
being that Laravel and Lumen use an `app` folder instead of the more general `src` folder.

## Facades
Laravel has a system where singletons can be called statically from any where using [facades](https://laravel.com/docs/5.6/facades)
Whilst this is great when working with Laravel, it means all application logic is tied to the facade.

It is almost always possible to use dependency injection instead of facades, this also allows easier mocking with full
IDE code completion on the mocked classes amongst other benefits.

The only true place to use Facades is inside Blade templates where it is cumbersome to inject full services.

## Contracts and Interfaces
Laravel uses the concept of contracts and suffixes all interfaces with this terminology. Laravel uses a container to 
resolve interfaces to the correct class which means that you should type hint to the contract where possible.

## Doctrine vs Eloquent
Eloquent is the default ORM and uses the active record pattern to interace with the database.
Whilst this is fine, we prefer to use Doctrine which is possible using
the [Laravel Doctrine Package](http://www.laraveldoctrine.org/) which generally
produces cleaner code than Eloquent.

There is no preference of the mapping driver used, but generally we use 
[Annotations](https://www.doctrine-project.org/projects/doctrine-annotations/en/latest/index.html) 
or [Doctrine Static Meta](https://github.com/edmondscommerce/doctrine-static-meta)

### Models/Entities
All models and entities should be kept in the same namespace/sub namespace - e.g: `App\Models`, `App\Models\System`. 

### Repositories
All logic for retrieving and persisting data should be kept inside a repository class. 
Like models, use a single namespace, e.g: `App\Repositories`, `App\Repositories\System`.

All models should have a repository bound to it.

## Testing
Whilst it is not required, we recommend that test suites are split in to directories under the `tests` directory.
For example:
* Tests\Unit
* Tests\Integration
* Tests\Functional
* Tests\Acceptance

[PHPUnit](https://phpunit.de/) is the defacto choice for testing and with the additional
[Laravel](https://laravel.com/docs/5.6/testing) and
[Doctrine](http://www.laraveldoctrine.org/docs/1.3/orm/testing) 
testing tools to help with database testing for your models and repositories.