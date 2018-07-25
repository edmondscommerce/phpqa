<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Small\Markdown;

use EdmondsCommerce\PHPQA\Markdown\LinksChecker;
use PHPUnit\Framework\TestCase;

/**
 * Class LinksCheckerTest
 *
 * @package EdmondsCommerce\PHPQA\Markdown
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @coversDefaultClass \EdmondsCommerce\PHPQA\Markdown\LinksChecker
 */
class LinksCheckerTest extends TestCase
{
    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @covers \EdmondsCommerce\PHPQA\Markdown\LinksChecker
     * @small
     */
    public function testInvalidProject(): void
    {
        $pathToProject    = __DIR__ . '/../../assets/linksChecker/projectWithBrokenLinks';
        $expectedExitCode = 1;
        $expectedOutput   = '
/docs/linksCheckerTest.md
-------------------------

Bad link for "incorrect link" to "./../nothere.md"

/README.md
----------

Bad link for "incorrect link" to "./foo.md"
';
        self::assertResult($pathToProject, $expectedExitCode, $expectedOutput);
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @covers \EdmondsCommerce\PHPQA\Markdown\LinksChecker
     * @small
     */
    public function testMainNoReadmeFile(): void
    {
        $this->expectException(\RuntimeException::class);
        LinksChecker::main(__DIR__ . '/../../assets/linksChecker/projectNoReadme');
    }

    /**
     * @throws \Exception
     * @covers \EdmondsCommerce\PHPQA\Markdown\LinksChecker
     * @small
     */
    public function testValidNoDocsFolder(): void
    {
        $pathToProject    = __DIR__ . '/../../assets/linksChecker/projectWithReadmeNoDocsFolder';
        $expectedExitCode = 0;
        $expectedOutput   = '';
        self::assertResult($pathToProject, $expectedExitCode, $expectedOutput);
    }

    /**
     * @covers \EdmondsCommerce\PHPQA\Markdown\LinksChecker
     * @small
     */
    public function testItHandlesNonFileLinks(): void
    {
        $pathToProject    = __DIR__ . '/../../assets/linksChecker/projectWithNonFileLinks';
        $expectedExitCode = 1;
        $expectedOutput   = '
/README.md
----------

Bad link for "invalid link" to "https://httpstat.us/404"
result: NULL
';
        self::assertResult($pathToProject, $expectedExitCode, $expectedOutput);
    }

    /**
     * @param string $pathToProject
     * @param int    $expectedExitCode
     * @param string $expectedOutput
     *
     * @throws \Exception
     */
    protected function assertResult(string $pathToProject, int $expectedExitCode, string $expectedOutput): void
    {
        ob_start();
        $actualExitCode = LinksChecker::main($pathToProject);
        $actualOutput   = ob_get_clean();
        self::assertSame($expectedOutput, $actualOutput);
        self::assertSame($expectedExitCode, $actualExitCode);
    }
}
