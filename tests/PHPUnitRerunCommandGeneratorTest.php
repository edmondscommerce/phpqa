<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

class PHPUnitRerunCommandGeneratorTest extends TestCase
{
    public function testCanParseFailuresAndErrors()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter    = $generator->main(__DIR__.'/assets/phpunitRerun/phpunit.junit.log.xml');
        $this->assertNotEmpty($filter);
        $this->assertNotEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }

    public function testWillReturnNoFilterIfLogPathDoesNotExist()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter    = $generator->main('/path/not/exists');
        $this->assertEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }

    public function testWillReturnNoFilterIfLogIsEmpty()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter    = $generator->main(__DIR__.'/assets/phpunitRerun/empty.phpunit.junit.log.xml');
        $this->assertEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }

    public function testWillReturnNoFilterIfNoFailures()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter    = $generator->main(__DIR__.'/assets/phpunitRerun/no.failures.phpunit.junit.log.xml');
        $this->assertEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }

    public function testItWillThrowExceptionIfXmlInvalid()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $this->expectException(\RuntimeException::class);
        $filter = $generator->main(__DIR__.'/assets/phpunitRerun/invalid.xml.phpunit.junit.log.xml');

    }
}
