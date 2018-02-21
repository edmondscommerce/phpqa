<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Config;
use PHPUnit\Framework\TestCase;

class LinksCheckerTest extends TestCase
{
    private $testMdFile;

    private $docsDir;

    private $readmeBackup;

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setup()
    {
        $root          = Config::getProjectRootDirectory();
        $this->docsDir = $root.'/docs';
        if (!is_dir($this->docsDir)) {
            mkdir($this->docsDir);
        }
        $this->testMdFile = $this->docsDir.'/test.md';
        if (!$this->readmeBackup) {
            $this->readmeBackup = file_get_contents(Config::getProjectRootDirectory().'/README.md');
        }
        file_put_contents(Config::getProjectRootDirectory().'/README.md', $this->readmeBackup);
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
        $expected = '
/docs/test.md
-------------

Bad link for "incorrect link" to "./../nothere.md"
';
        ob_start();
        LinksChecker::main();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMainNoDocsFolder()
    {
        if (is_dir($this->docsDir)) {
            $docs = glob("$this->docsDir/*");
            if (count($docs) > 2) {
                throw new \Exception('Found more than 2 docs to delete - that should not happen');
            }
            array_map('unlink', $docs);
            rmdir($this->docsDir);
        }
        ob_start();
        LinksChecker::main();
        $actual = ob_get_clean();
        $this->assertEmpty($actual);
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testMainNoReadmeFile()
    {
        unlink(Config::getProjectRootDirectory().'/README.md');
        $this->expectException(\RuntimeException::class);
        LinksChecker::main();
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __destruct()
    {
        if (!empty($this->testMdFile) && file_exists($this->testMdFile)) {
            unlink($this->testMdFile);
        }
        file_put_contents(Config::getProjectRootDirectory().'/README.md', $this->readmeBackup);
    }
}
