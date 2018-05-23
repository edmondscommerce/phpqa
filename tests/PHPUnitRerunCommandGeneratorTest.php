<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

class PHPUnitRerunCommandGeneratorTest extends TestCase
{
    public function testCanParseFailuresAndErrors()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter   = $generator->main(__DIR__.'/assets/phpunit.junit.log.xml');
        $this->assertNotEmpty($filter);
        $this->assertNotEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }

    public function testWillReturnNoFilterIfLogPathDoesNotExist()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter   = $generator->main('/path/not/exists');
        $this->assertEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }

    public function testWillReturnNoFilterIfLogIsEmpty()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $filter   = $generator->main(__DIR__.'/assets/empty.phpunit.junit.log.xml');
        $this->assertEquals(PHPUnitRerunCommandGenerator::NO_FILTER, $filter);
    }
}
