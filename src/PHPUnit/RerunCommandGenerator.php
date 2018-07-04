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

    /**
     * @var array
     */
    private $toRerun = [];

    public function main(string $junitLogPath = null): string
    {
        $this->toRerun = [];
        $this->logPath = $junitLogPath ?? $this->getDefaultFilePath();
        if (!file_exists($this->logPath)) {
            return self::NO_FILTER;
        }
        $contents = (string)file_get_contents($this->logPath);
        if ('' === $contents) {
            return self::NO_FILTER;
        }
        $failureNodes = $this->getFailureNodes($contents);
        foreach ($failureNodes as $testCaseNode) {
            $attributes = $testCaseNode->attributes();
            if ($attributes instanceof \SimpleXMLElement) {
                list($class, $name) = $this->getClassAndName($attributes);
                $this->toRerun[$class][] = $name;
            }
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

    private function getClassAndName(\SimpleXMLElement $attributes): array
    {
        $class = $name = null;
        foreach ($attributes as $attribute) {
            if ('class' === $attribute->getName()) {
                $class = str_replace('\\', '\\\\', $attribute->__toString());
                continue;
            }
            if ('name' === $attribute->getName()) {
                $name = $attribute->__toString();
                continue;
            }
        }
        if (null === $class || null === $name) {
            throw new \RuntimeException(
                'Failed finding the class and/or name in the attributes:'.$attributes->__toString()
            );
        }

        return [$class, $name];
    }

    /**
     * @param string $contents
     *
     * @return array|\SimpleXMLElement[]
     */
    private function getFailureNodes(string $contents): array
    {
        $this->load($contents);

        $nodes = $this->simpleXml->xpath(
            '//testsuite/testcase[error] | //testsuite/testcase[failure] '
            .'| //testsuite/testcase[skipped] | //testsuite/testcase[incomplete]'
        );
        if (false === $nodes) {
            return [];
        }

        return $nodes;
    }


    private function load(string $contents): void
    {

        \libxml_use_internal_errors(true);
        $sXml = \simplexml_load_string($contents);
        if (false === $sXml) {
            $message = "Failed loading XML\n";
            foreach (libxml_get_errors() as $error) {
                $message .= "\n\t".$error->message;
            }
            throw new \RuntimeException($message);
        }
        $this->simpleXml = $sXml;
    }

    /**
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getDefaultFilePath(): string
    {
        return Helper::getProjectRootDirectory().'/var/qa/phpunit.junit.log.xml';
    }
}
