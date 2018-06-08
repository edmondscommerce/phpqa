<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\PHPUnit;

use EdmondsCommerce\PHPQA\Helper;

class RerunCommandGenerator
{
    /**
     * no failed tests so just include all
     * however, this can confuse bash - try using `set -f` to disable globbing
     */
    public const NO_FILTER = '/.*/';

    /**
     * @var string
     */
    private $logPath;

    /**
     * @var \SimpleXMLElement
     */
    private $simpleXml;

    private $toRerun = [];

    public function main(string $junitLogPath = null): string
    {
        $this->toRerun = [];
        $this->logPath = $junitLogPath ?? $this->getDefaultFilePath();
        if (!file_exists($this->logPath)) {
            return self::NO_FILTER;
        }
        $contents = file_get_contents($this->logPath);
        if ('' === $contents) {
            return self::NO_FILTER;
        }
        $this->load($contents);
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
            return self::NO_FILTER;
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


    protected function load(string $contents)
    {

        libxml_use_internal_errors(true);
        $this->simpleXml = simplexml_load_string($contents);
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
        return Helper::getProjectRootDirectory().'/var/qa/phpunit.junit.log.xml';
    }
}
