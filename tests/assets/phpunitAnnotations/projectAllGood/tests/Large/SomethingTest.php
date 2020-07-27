<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\assets\phpunitAnnotations\projectAllGood\tests\Small;

use Exception;
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
     * @throws Exception
     * @covers ::somethingThings()
     * @large
     * @test
     */
    public function itDoesSomething(): void
    {
    }

    /**
     * @throws Exception
     * @covers ::somethingThings()
     * @large
     */
    public function testItDoesSomething(): void
    {
    }
}
