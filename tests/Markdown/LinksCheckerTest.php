<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Config;
use PHPUnit\Framework\TestCase;

class LinksCheckerTest extends TestCase
{
    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMain()
    {
        $root     = Config::getProjectRootDirectory();
        $expected = "\n$root".'/docs/test.md
----------------------------------

Bad link for "incorrect link" to "./../nothere.md"
';
        ob_start();
        LinksChecker::main();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);

    }
}
