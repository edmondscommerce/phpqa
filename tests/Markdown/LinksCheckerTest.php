<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Config;
use PHPUnit\Framework\TestCase;

class LinksCheckerTest extends TestCase
{
    private $testMdFile;

    public function setup()
    {
        $root             = Config::getProjectRootDirectory();
        $this->testMdFile = $root.'/docs/test.md';
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMain()
    {

        file_put_contents($this->testMdFile, <<<MARKDOWN
This is a test file for markdown linting

[correct link](./../README.md) should work

[incorrect link](./../nothere.md) should not work
MARKDOWN
        );
        $expected = "\n$this->testMdFile
----------------------------------

Bad link for \"incorrect link\" to \"./../nothere.md\"
";
        ob_start();
        LinksChecker::main();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);
    }

    public function __destruct()
    {
        if (file_exists($this->testMdFile)) {
            unlink($this->testMdFile);
        }
    }
}
