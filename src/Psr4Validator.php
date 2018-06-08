<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

class Psr4Validator
{

    /**
     * @var string
     */
    protected $pathToProjectRoot;
    /**
     * @var array
     */
    protected $decodedComposerJson;
    private   $parseErrors  = [];
    private   $psr4Errors   = [];
    private   $ignoreRegexPatterns;
    private   $ignoredFiles = [];
    private   $missingPaths = [];

    /**
     * Psr4Validator constructor.
     *
     * @param array  $ignoreRegexPatterns Set of regex patterns used to exclude files or directories
     * @param string $pathToProjectRoot
     * @param array  $decodedComposerJson
     */
    public function __construct(array $ignoreRegexPatterns, string $pathToProjectRoot, array $decodedComposerJson)
    {
        $this->ignoreRegexPatterns = $ignoreRegexPatterns;
        $this->pathToProjectRoot   = $pathToProjectRoot;
        $this->decodedComposerJson = $decodedComposerJson;
    }

    /**
     * @throws \Exception
     */
    public function main(): array
    {
        $this->loop();
        $errors = [];
        //Actual Errors
        if (!empty($this->psr4Errors)) {
            $errors['PSR-4 Errors:'] = $this->psr4Errors;
        }
        if (!empty($this->parseErrors)) {
            $errors['Parse Errors:'] = $this->parseErrors;
        }
        if (!empty($this->missingPaths)) {
            $errors['Missing Paths:'] = $this->missingPaths;
        }
        if ([] === $errors) {
            return $errors;
        }
        //Debug Info
        if (!empty($this->ignoredFiles)) {
            $errors['Ignored Files:'] = $this->ignoredFiles;
        }

        return $errors;
    }

    /**
     * @throws \Exception
     */
    private function loop()
    {
        foreach ($this->yieldPhpFilesToCheck() as list($absPathRoot, $namespaceRoot, $fileInfo)) {
            $this->check($absPathRoot, $namespaceRoot, $fileInfo);
        }
    }

    private function check(string $absPathRoot, string $namespaceRoot, \SplFileInfo $fileInfo)
    {
        $actualNamespace = $this->getActualNamespace($fileInfo);
        if ('' === $actualNamespace) {
            return;
        }
        $expectedNamespace = $this->expectedFileNamespace($absPathRoot, $namespaceRoot, $fileInfo);
        if ($actualNamespace !== $expectedNamespace) {
            $this->psr4Errors[$namespaceRoot][] =
                [
                    'fileInfo'          => $fileInfo->getRealPath(),
                    'expectedNamespace' => $expectedNamespace,
                    'actualNamespace'   => $actualNamespace,
                ];
        }
    }

    private function expectedFileNamespace(string $absPathRoot, string $namespaceRoot, \SplFileInfo $fileInfo): string
    {
        $relativePath = \substr($fileInfo->getPathname(), \strlen($absPathRoot));
        $relativeDir  = \dirname($relativePath);
        $relativeNs   = '';
        if ($relativeDir !== '.') {
            $relativeNs = \str_replace(
                '/',
                '\\',
                \ltrim($relativeDir, '/')
            );
        }

        return rtrim($namespaceRoot.$relativeNs, '\\');
    }

    private function getActualNamespace(\SplFileInfo $fileInfo): string
    {
        $contents = \file_get_contents($fileInfo->getPathname());
        \preg_match('%namespace\s+?([^;]+)%', $contents, $matches);
        if (empty($matches)) {
            $this->parseErrors[] = $fileInfo->getRealPath();

            return '';
        }

        return $matches[1];
    }

    /**
     * @param string $realPath
     *
     * @return \SplHeap|\SplFileInfo[]
     */
    private function getDirectoryIterator(string $realPath)
    {
        $directoryIterator = new \RecursiveDirectoryIterator(
            $realPath,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iterator          = new \RecursiveIteratorIterator(
            $directoryIterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        return new class($iterator) extends \SplHeap
        {
            public function __construct(\RecursiveIteratorIterator $iterator)
            {
                foreach ($iterator as $item) {
                    $this->insert($item);
                }
            }

            public function compare($item1, $item2)
            {
                return strcmp($item2->getRealpath(), $item1->getRealpath());
            }
        };
    }

    private function addMissingPathError(string $path, string $namespaceRoot, string $absPathRoot)
    {
        $invalidPathMessage = "Namespace root '$namespaceRoot'".
                              "\ncontains a path '$path''".
                              "\nwhich doesn't exist\n";
        if (stripos($absPathRoot, "Magento") !== false) {
            $invalidPathMessage .= 'Magento\'s composer includes this by default, '
                                   .'it should be removed from the psr-4 section';
        }
        $this->missingPaths[$path] = $invalidPathMessage;
    }

    /**
     * @return \Generator
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function yieldPhpFilesToCheck(): \Generator
    {
        $json = $this->decodedComposerJson;
        foreach (['autoload', 'autoload-dev'] as $autoload) {
            if (!isset($json[$autoload]['psr-4'])) {
                continue;
            }
            $psr4 = $json[$autoload]['psr-4'];
            foreach ($psr4 as $namespaceRoot => $paths) {
                if (!\is_array($paths)) {
                    $paths = [$paths];
                }
                foreach ($paths as $path) {
                    $absPathRoot     = $this->pathToProjectRoot.'/'.$path;
                    $realAbsPathRoot = \realpath($absPathRoot);
                    if (false === $realAbsPathRoot) {
                        $this->addMissingPathError($path, $namespaceRoot, $absPathRoot);
                        continue;
                    }
                    $iterator = $this->getDirectoryIterator($absPathRoot);
                    foreach ($iterator as $fileInfo) {
                        if ('php' !== $fileInfo->getExtension()) {
                            continue;
                        }
                        foreach ($this->ignoreRegexPatterns as $pattern) {
                            if (\preg_match($pattern, $fileInfo->getRealPath())) {
                                $this->ignoredFiles[] = $fileInfo->getRealPath();
                                continue 2;
                            }
                        }
                        yield [
                            $absPathRoot,
                            $namespaceRoot,
                            $fileInfo,
                        ];
                    }
                }
            }
        }
    }
}
