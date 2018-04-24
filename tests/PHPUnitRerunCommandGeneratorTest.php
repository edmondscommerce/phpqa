<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

class PHPUnitRerunCommandGeneratorTest extends TestCase
{


    public function testCanParseFailuresAndErrors()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $command   = $generator->getRerunCommandFromFile(__DIR__.'/assets/phpunit.junit.log.xml');
        $this->assertNotEmpty($command);
    }
}
