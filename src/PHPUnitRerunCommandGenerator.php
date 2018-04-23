<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use Symfony\Component\DomCrawler\Crawler;

class PHPUnitRerunCommandGenerator
{
    private $logPath;

    private $log;
    /**
     * @var Crawler
     */
    private $crawler;

    private $failedTests = [];

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function getRerunCommandFromFile(?string $junitLogPath = null)
    {
        $this->logPath = $junitLogPath ?? $this->getDefaultFilePath();
        $this->load();
        $this->crawler->filterXPath(
            '/testsuite/testcase[error] | /testsuite/testcase[error]'
        )->each(
            function (Crawler $subCrawler) {
                $subCrawler;
            }
        );
    }


    protected function load()
    {
        $this->crawler->addXmlContent(file_get_contents($this->logPath));
    }

    protected function getDefaultFilePath(): string
    {
        return Config::getProjectRootDirectory().'/var/qa/phpunit.junit.log.xml';

    }

}
