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

    /**
     * no failed tests so just include all
     * however, this can confuse bash - try using `set -f` to disable globbing
     */
    protected function noFilter(): string
    {
        return '/.*/';
    }

    public function main(string $junitLogPath = null): string
    {
        $this->toRerun = [];
        $this->logPath = $junitLogPath ?? $this->getDefaultFilePath();
        if (!file_exists($this->logPath)) {
            return $this->noFilter();
        }
        $contents = file_get_contents($this->logPath);
        if ('' === $contents) {
            return $this->noFilter();
        }
        $this->load();
        $failureNodes = $this->simpleXml->xpath(
            '//testsuite/testcase[error] | //testsuite/testcase[failure] '
            .'| //testsuite/testcase[skipped] | //testsuite/testcase[incomplete]'
        );
        foreach ($failureNodes as $testCaseNode) {
            $attributes              = $testCaseNode->attributes();
            $class                   = str_replace('\\', '\\\\', (string)$attributes->class);
            $this->toRerun[$class][] = (string)$attributes->name;
        }
        if ($this->toRerun === []) {
            return $this->noFilter();
        }
        $command = '/(';
        foreach ($this->toRerun as $class => $testNames) {
            foreach ($testNames as $testName) {
                $command .= "$class::$testName|";
            }
        }
        $command = rtrim($command, '|');
        $command .= ')/';

        return $command;
    }


    protected function load()
    {

        libxml_use_internal_errors(true);
        $this->simpleXml = simplexml_load_string();
        if (false === $this->simpleXml) {
            $message = "Failed loading XML\n";
            foreach (libxml_get_errors() as $error) {
                $message .= "\n\t".$error->message;
            }
            throw new \RuntimeException($message);
        }
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
