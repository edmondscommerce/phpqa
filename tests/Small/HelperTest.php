<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Small;

use EdmondsCommerce\PHPQA\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Class HelperTest
 *
 * @package EdmondsCommerce\PHPQA
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @coversDefaultClass \EdmondsCommerce\PHPQA\Helper
 */
class HelperTest extends TestCase
{
    /**
     * @throws \Exception
     * @covers ::getComposerJsonDecoded()
     * @covers ::getProjectRootDirectory()
     * @small
     */
    public function testItCanGetComposerJsonDecode()
    {
        $actual = Helper::getComposerJsonDecoded();
        $this->assertNotEmpty($actual);
    }

    /**
     * @throws \Exception
     * @covers ::getComposerJsonDecoded()
     * @small
     */
    public function testItWillThrowExceptionForInvalidComposerJson()
    {
        $this->expectException(\RuntimeException::class);
        Helper::getComposerJsonDecoded(__DIR__.'/../assets/helper/invalid.composer.json');
    }

    /**
     * @throws \Exception
     * @covers ::getProjectRootDirectory()
     * @small
     */
    public function testGetProjectRoot()
    {
        $expected = \realpath(__DIR__.'/../../../phpqa/');
        $actual   = Helper::getProjectRootDirectory();
        $this->assertSame($expected, $actual);
    }
}
