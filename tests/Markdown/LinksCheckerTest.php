<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use PHPUnit\Framework\TestCase;

class LinksCheckerTest extends TestCase
{
    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMain()
    {
        $expected = '
/docs/linksCheckerTest.md
-------------------------

Bad link for "incorrect link" to "./../nothere.md"
';
        ob_start();
        LinksChecker::main(__DIR__.'/../assets/linksChecker/projectWithReadme');
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMainNoReadmeFile()
    {
        $this->expectException(\RuntimeException::class);
        LinksChecker::main(__DIR__.'/../assets/linksChecker/projectNoReadme');
    }
}
