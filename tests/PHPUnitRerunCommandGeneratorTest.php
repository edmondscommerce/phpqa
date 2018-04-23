<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class PHPUnitRerunCommandGeneratorTest extends TestCase
{
    public function testCanParseFailuresAndErrors()
    {
        $generator = new PHPUnitRerunCommandGenerator(new Crawler());
        $generator->getRerunCommandFromFile();
    }
}
