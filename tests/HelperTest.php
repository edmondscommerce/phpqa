<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

/**
 * Class HelperTest
 *
 * @package EdmondsCommerce\PHPQA
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HelperTest extends TestCase
{
    public function testItCanGetComposerJsonDecode()
    {
        $actual = Helper::getComposerJsonDecoded();
        $this->assertNotEmpty($actual);
    }

    public function testItWillThrowExceptionForInvalidComposerJson()
    {
        $this->expectException(\RuntimeException::class);
        Helper::getComposerJsonDecoded(__DIR__.'/assets/helper/invalid.composer.json');
    }

    public function testGetProjectRoot()
    {
        $expected = \realpath(__DIR__.'/../');
        $actual   = Helper::getProjectRootDirectory();
        $this->assertSame($expected, $actual);
    }
}
