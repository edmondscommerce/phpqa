<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\assets\phpunitAnnotations\projectMissingSmall\tests\Small;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 *
 * @small
 */
final class SomethingTest extends TestCase
{
    /**
     * @smaalll
     * @test
     */
    public function itDoesSomething(): void
    {
    }
}
