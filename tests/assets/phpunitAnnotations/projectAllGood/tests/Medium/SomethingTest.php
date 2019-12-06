<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\assets\phpunitAnnotations\projectAllGood\tests\Medium;

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
     * @medium
     * @test
     */
    public function itDoesSomething(): void
    {
    }
}
