<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

class PHPUnitRerunCommandGenerator
{
    /**
     * @var string
     */
    private $logPath;

    /**
     * @var \SimpleXMLElement
     */
    private $simpleXml;

    private $toRerun = [];

    public function __construct()
    {
    }

    public function main(string $junitLogPath = null)
    {
        $this->toRerun = [];
        $this->logPath = $junitLogPath ?? $this->getDefaultFilePath();
        $this->load();
        $failureNodes = $this->simpleXml->xpath(
            '//testsuite/testcase[error] | //testsuite/testcase[failure]'
        );
        foreach ($failureNodes as $testCaseNode) {
            $attributes                                  = $testCaseNode->attributes();
            $this->toRerun[(string)$attributes->class][] = (string)$attributes->name;
        }
        if ($this->toRerun === []) {
            return '';
        }
        $command = ' --filter "(';
        foreach ($this->toRerun as $class => $testNames) {
            foreach ($testNames as $testName) {
                $command .= "$class::$testName|";
            }
        }
        $command = rtrim($command, '|');
        $command .= ')"';

        return $command;
    }


    protected function load()
    {
        $this->simpleXml = simplexml_load_string(file_get_contents($this->logPath));
    }

    /**
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getDefaultFilePath(): string
    {
        return Config::getProjectRootDirectory().'/var/qa/phpunit.junit.log.xml';
    }
}
