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
class CheckForLargeAndMediumAnnotations
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
                '$pathToTestsDirectory "'.$pathToTestsDirectory.'" does not exist"'
            );
        }
        $this->largePath  = $pathToTestsDirectory.'/Large';
        $this->mediumPath = $pathToTestsDirectory.'/Medium';
        $this->checkLarge();
        $this->checkMedium();

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

    private function checkDirectory(string $path, string $annotation)
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
    private function checkFile(\SplFileInfo $fileInfo, string $annotation)
    {
        $contents = file_get_contents($fileInfo->getPathname());
        $matches  = [];
        preg_match_all(
            <<<REGEXP
%(?<docblock>/\*(?:[^*]|\n|(?:\*(?:[^/]|\n)))*\*/)\s+?public\s+?function\s+?(?<method>.+?)\(%
REGEXP
            .'si',
            $contents,
            $matches
        );
        if (empty($matches[0])) {
            $this->errors[$fileInfo->getFilename()][] = 'Failed finding any doc blocks';

            return;
        }
        foreach ($matches['docblock'] as $key => $docblock) {
            if (false !== strpos($docblock, '@'.$annotation)) {
                continue;
            }
            $this->errors[$fileInfo->getFilename()][] =
                'Failed finding @'.$annotation.' for method: '.$matches['method'][$key];
        }

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
