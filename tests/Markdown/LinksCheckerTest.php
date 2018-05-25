<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use PHPUnit\Framework\TestCase;

/**
 * Class LinksCheckerTest
 *
 * @package EdmondsCommerce\PHPQA\Markdown
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class LinksCheckerTest extends TestCase
{
    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testInvalidProject()
    {
        $pathToProject    = __DIR__.'/../assets/linksChecker/projectWithBrokenLinks';
        $expectedExitCode = 1;
        $expectedOutput   = '
/docs/linksCheckerTest.md
-------------------------

Bad link for "incorrect link" to "./../nothere.md"

/README.md
----------

Bad link for "incorrect link" to "./foo.md"
';
        $this->assertResult($pathToProject, $expectedExitCode, $expectedOutput);
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
        $pathToProject    = __DIR__.'/../assets/linksChecker/projectWithReadmeNoDocsFolder';
        $expectedExitCode = 0;
        $expectedOutput   = '';
        $this->assertResult($pathToProject, $expectedExitCode, $expectedOutput);
    }

    public function testItHandlesNonFileLinks()
    {
        $pathToProject    = __DIR__.'/../assets/linksChecker/projectWithNonFileLinks';
        $expectedExitCode = 1;
        $expectedOutput   = '
/README.md
----------

Bad link for "invalid link" to "https://httpstat.us/404"
result: NULL
';
        $this->assertResult($pathToProject, $expectedExitCode, $expectedOutput);
    }

    protected function assertResult(string $pathToProject, int $expectedExitCode, string $expectedOutput)
    {
        ob_start();
        $actualExitCode = LinksChecker::main($pathToProject);
        $actualOutput   = ob_get_clean();
        $this->assertSame($expectedOutput, $actualOutput);
        $this->assertSame($expectedExitCode, $actualExitCode);
    }
}
