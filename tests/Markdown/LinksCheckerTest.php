<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use PHPUnit\Framework\TestCase;

class LinksCheckerTest extends TestCase
{
    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testInvalidProject()
    {
        $expected = '
/docs/linksCheckerTest.md
-------------------------

Bad link for "incorrect link" to "./../nothere.md"

/README.md
----------

Bad link for "incorrect link" to "./foo.md"
';
        ob_start();
        LinksChecker::main(__DIR__.'/../assets/linksChecker/projectWithBrokenLinks');
        $actual = ob_get_clean();
        $this->assertSame($expected, $actual);
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

    public function testValidNoDocsFolder()
    {
        $expected = 0;
        $actual   = LinksChecker::main(__DIR__.'/../assets/linksChecker/projectWithReadmeNoDocsFolder');
        $this->assertSame($expected, $actual);
    }

    public function testItHandlesNonFileLinks()
    {
        $expected       = 1;
        $expectedOutput = '
/README.md
----------

Bad link for "invalid link" to "http://no.no.no"
';
        ob_start();
        $actual       = LinksChecker::main(__DIR__.'/../assets/linksChecker/projectWithNonFileLinks');
        $actualOutput = ob_get_clean();
        $this->assertSame($expected, $actual);
        $this->assertSame($expectedOutput, $actualOutput);
    }

}
