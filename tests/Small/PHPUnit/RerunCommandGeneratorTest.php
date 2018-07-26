<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Small\PHPUnit;

use EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class RerunCommandGeneratorTest
 *
 * @package EdmondsCommerce\PHPQA\Tests\Small\PHPUnit
 * @coversDefaultClass \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
 */
class RerunCommandGeneratorTest extends TestCase
{
    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testCanParseFailuresAndErrors(): void
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main(__DIR__ . '/../../assets/phpunitRerun/phpunit.junit.log.xml');
        self::assertNotEmpty($filter);
        self::assertNotEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testWillReturnNoFilterIfLogPathDoesNotExist(): void
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main('/path/not/exists');
        self::assertEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testWillReturnNoFilterIfLogIsEmpty(): void
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main(__DIR__ . '/../../assets/phpunitRerun/empty.phpunit.junit.log.xml');
        self::assertEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testWillReturnNoFilterIfNoFailures(): void
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main(__DIR__ . '/../../assets/phpunitRerun/no.failures.phpunit.junit.log.xml');
        self::assertEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testItWillThrowExceptionIfXmlInvalid(): void
    {
        $generator = new RerunCommandGenerator();
        $this->expectException(\RuntimeException::class);
        $generator->main(__DIR__ . '/../../assets/phpunitRerun/invalid.xml.phpunit.junit.log.xml');
    }
}
