<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

class PHPUnitRerunCommandGeneratorTest extends TestCase
{


    public function testCanParseFailuresAndErrors()
    {
        $generator = new PHPUnitRerunCommandGenerator();
        $command   = $generator->main(__DIR__.'/assets/phpunit.junit.log.xml');
        $this->assertNotEmpty($command);
    }

    /**
     * This is only used for manually testing the process
     */
    public function alwaysFail()
    {
        $this->assertTrue(false);
    }
}
