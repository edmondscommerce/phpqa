<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\PHPUnit;

/**
 * Class CheckForLargeAndMediumAnnotations
 *
 * This class checks a test directory structure and if it is using the Edmonds Commerce recommended style of a
 * `Large`,`Medium` and `Large` sub directory structure, then we will also ensure that the large and medium tests are
 * correctly annotated
 *
 * @package EdmondsCommerce\PHPQA\PHPUnit
 */
class CheckAnnotations
{
    /**
     * @var string
     */
    private $largePath;
    /**
     * @var string
     */
    private $mediumPath;

    /**
     * @var string
     */
    private $smallPath;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * Check the Large and Medium directories, if they exist, and then assert that all tests have the correct annotation
     *
     * @param string $pathToTestsDirectory
     *
     * @return array of errors
     */
    public function main(string $pathToTestsDirectory): array
    {
        if (!is_dir($pathToTestsDirectory)) {
            throw new \InvalidArgumentException(
                '$pathToTestsDirectory "' . $pathToTestsDirectory . '" does not exist"'
            );
        }
        $this->largePath  = $pathToTestsDirectory . '/Large';
        $this->mediumPath = $pathToTestsDirectory . '/Medium';
        $this->smallPath  = $pathToTestsDirectory . '/Small';
        $this->checkLarge();
        $this->checkMedium();
        $this->checkSmall();

        return $this->errors;
    }

    private function checkLarge(): void
    {
        if (!is_dir($this->largePath)) {
            return;
        }
        $this->checkDirectory($this->largePath, 'large');
    }

    private function checkMedium(): void
    {
        if (!is_dir($this->mediumPath)) {
            return;
        }
        $this->checkDirectory($this->mediumPath, 'medium');
    }

    private function checkSmall(): void
    {
        if (!is_dir($this->smallPath)) {
            return;
        }
        $this->checkDirectory($this->smallPath, 'small');
    }

    private function checkDirectory(string $path, string $annotation): void
    {
        foreach ($this->yieldTestFilesInPath($path) as $fileInfo) {
            if (false === strpos($fileInfo->getFilename(), 'Test.php')) {
                continue;
            }
            $this->checkFile($fileInfo, $annotation);
        }
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param string       $annotation
     *
     */
    private function checkFile(\SplFileInfo $fileInfo, string $annotation): void
    {
        $contents = (string)file_get_contents($fileInfo->getPathname());
        if ($this->isAnnotationInClassDocBlock($contents, $annotation) === true) {
            return;
        }

        $matches  = [];
        preg_match_all(
            <<<REGEXP
%(?<docblock>/\*(?:[^*]|\n|(?:\*(?:[^/]|\n)))*\*/|\n)\s+?public\s+?function\s+?(?<method>.+?)\(%
REGEXP
            . 'si',
            $contents,
            $matches
        );
        if ('' === $matches[0]) {
            $this->errors[$fileInfo->getFilename()][] = 'Failed finding any doc blocks';

            return;
        }
        foreach ($matches['method'] as $key => $method) {
            $docblock = $matches['docblock'][$key];
            /* Found the annotation - continue */
            if (false !== \strpos($docblock, '@' . $annotation)) {
                continue;
            }
            /* No @test annotation found & method not beginning test =  not a test, so continue */
            if (false === \strpos($docblock, '@test') && false === \strpos($method, 'test')) {
                continue;
            }
            $this->errors[$fileInfo->getFilename()][] =
                'Failed finding @' . $annotation . ' for method: ' . $method;
        }
    }

    /**
     * It is possible to put the annotation in the class doc, and have it apply to all tests in the file. This checks
     * the class docblock and if the annotation is found there returns true. If not it returns false and the rest of the
     * self::checkFile method runs looking for it in the method docblocks
     *
     * @param string $fileContent
     * @param string $annotation
     *
     * @return bool
     */
    private function isAnnotationInClassDocBlock(string $fileContent, string $annotation):bool
    {
        $matches = [];
        preg_match_all(
            <<<REGEXP
%(?<docblock>/\*(?:[^*]|\n|(?:\*(?:[^/]|\n)))*\*/)\s+?class\s+?(?<classname>.+?)\s+?extends%
REGEXP
            . 'si',
            $fileContent,
            $matches
        );
        if (count($matches['docblock']) !== 1) {
            return false;
        }
        $docBlock = array_shift($matches['docblock']);
        return strpos($docBlock, '@' . $annotation) !== false;
    }


    /**
     * @param string $path
     *
     * @return \Generator|\SplFileInfo[]
     */
    private function yieldTestFilesInPath(string $path): \Generator
    {
        $recursiveDirectoryIterator = new \RecursiveDirectoryIterator($path);
        $iterator                   = new \RecursiveIteratorIterator($recursiveDirectoryIterator);
        foreach ($iterator as $fileInfo) {
            yield $fileInfo;
        }
    }
}
