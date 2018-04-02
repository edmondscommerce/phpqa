<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

class Psr4Validator
{

    private $parseErrors  = [];
    private $psr4Errors   = [];
    private $ignoreRegexPatterns;
    private $ignoredFiles = [];

    /**
     * Psr4Validator constructor.
     *
     * @param array $ignoreRegexPatterns Set of regex patterns used to exclude files or directories
     */
    public function __construct(array $ignoreRegexPatterns)
    {
        $this->ignoreRegexPatterns = $ignoreRegexPatterns;
    }

    /**
     * @throws \Exception
     */
    public function main()
    {
        $this->loop();
        if (empty($this->psr4Errors) && empty($this->parseErrors)) {
            return;
        }
        $errors = [];
        if (!empty($this->psr4Errors)) {
            $errors['PSR-4 Errors:'] = $this->psr4Errors;
        }
        if (!empty($this->parseErrors)) {
            $errors['Parse Errors:'] = $this->parseErrors;
        }
        if (!empty($this->ignoredFiles)) {
            $errors['Ignored Files:'] = $this->ignoredFiles;
        }
        echo "\nErrors found:\n"
             .\var_export($errors, true);
        throw new \RuntimeException(
            'Errors validating PSR4'
        );
    }

    /**
     * @throws \Exception
     */
    private function loop()
    {
        foreach ($this->yieldPhpFilesToCheck() as [$absPathRoot, $namespaceRoot, $fileInfo]) {
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
        if (empty($matches) || !isset($matches[1])) {
            $this->parseErrors[] = $fileInfo->getRealPath();

            return '';
        }

        return $matches[1];
    }

    /**
     * @param string $path
     *
     * @return \RecursiveIteratorIterator|\SplFileInfo[]
     */
    private function getDirectoryIterator(string $path): \RecursiveIteratorIterator
    {
        $directoryIterator = new \RecursiveDirectoryIterator(
            \realpath($path),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iterator          = new \RecursiveIteratorIterator(
            $directoryIterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        return $iterator;
    }

    /**
     * @throws \Exception
     */
    private function getComposerJson(): array
    {
        $path    = Config::getProjectRootDirectory().'/composer.json';
        $decoded = \json_decode(\file_get_contents($path), true);
        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new \RuntimeException('Failed loading composer.json: '.\json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * @return \Generator
     * @throws \Exception
     */
    private function yieldPhpFilesToCheck(): \Generator
    {
        $json = $this->getComposerJson();
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
                    $absPathRoot = Config::getProjectRootDirectory().'/'.$path;
                    $iterator    = $this->getDirectoryIterator($absPathRoot);
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
