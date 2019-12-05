<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\assets\phpunitAnnotations\projectAllGood\tests\Small;

use PHPUnit\Framework\TestCase;

/**
 * Class ClassDocsTest.
 *
 * @small
 *
 * @internal
 * @coversNothing
 */
final class ClassDocsTest extends TestCase
{
    /**
     * @test
     */
    public function testWithNoAnnotation(): void
    {
    }
}
