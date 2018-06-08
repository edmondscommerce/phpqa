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
    public function testCanParseFailuresAndErrors()
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main(__DIR__.'/../../assets/phpunitRerun/phpunit.junit.log.xml');
        $this->assertNotEmpty($filter);
        $this->assertNotEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testWillReturnNoFilterIfLogPathDoesNotExist()
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main('/path/not/exists');
        $this->assertEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testWillReturnNoFilterIfLogIsEmpty()
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main(__DIR__.'/../../assets/phpunitRerun/empty.phpunit.junit.log.xml');
        $this->assertEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testWillReturnNoFilterIfNoFailures()
    {
        $generator = new RerunCommandGenerator();
        $filter    = $generator->main(__DIR__.'/../../assets/phpunitRerun/no.failures.phpunit.junit.log.xml');
        $this->assertEquals(RerunCommandGenerator::NO_FILTER, $filter);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\RerunCommandGenerator
     * @small
     */
    public function testItWillThrowExceptionIfXmlInvalid()
    {
        $generator = new RerunCommandGenerator();
        $this->expectException(\RuntimeException::class);
        $generator->main(__DIR__.'/../../assets/phpunitRerun/invalid.xml.phpunit.junit.log.xml');
    }
}
