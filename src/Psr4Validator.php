<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

class Psr4Validator
{

    private $parseErrors = [];
    private $psr4Errors  = [];

    /**
     * @throws \Exception
     */
    public function main()
    {
        $this->loop();
        if (empty($this->psr4Errors) && empty($this->parseErrors)) {
            throw new \RuntimeException(
                'Errors validating PSR4:'.\print_r(
                    [
                        'PSR-4 Errors:' => $this->psr4Errors,
                        'Parse Errors:' => $this->parseErrors,
                    ],
                    true
                )
            );
        }
    }

    /**
     * @throws \Exception
     */
    private function loop(): void
    {
        foreach ($this->yieldPhpFilesToCheck() as [$absPathRoot, $namespaceRoot, $fileInfo]) {
            $this->check($absPathRoot, $namespaceRoot, $fileInfo);
        }
    }

    private function check(string $absPathRoot, string $namespaceRoot, \SplFileInfo $fileInfo): void
    {
        $actualNamespace = $this->getActualNamespace($fileInfo);
        if ('' === $actualNamespace) {
            return;
        }
        $expectedNamespace = $this->expectedFileNamespace($absPathRoot, $namespaceRoot, $fileInfo);
        if ($actualNamespace !== $expectedNamespace) {
            $this->psr4Errors[$namespaceRoot][] =
                [
                    'fileInfo'          => $fileInfo,
                    'expectedNamespace' => $expectedNamespace,
                    'actualNamespace'   => $actualNamespace,
                ];
        }
    }

    private function expectedFileNamespace(string $absPathRoot, string $namespaceRoot, \SplFileInfo $fileInfo): string
    {
        $relativePath = \substr($fileInfo->getPathname(), \strlen($absPathRoot));
        $relativeDir  = \dirname($relativePath);
        $relativeNs   = \str_replace('/', '\\', $relativeDir);

        return $namespaceRoot.$relativeNs;
    }

    private function getActualNamespace(\SplFileInfo $fileInfo): string
    {
        $contents = \file_get_contents($fileInfo->getPathname());
        \preg_match('%namespace\s+?([^;]+)%', $contents, $matches);
        if (empty($matches) || !isset($matches[1])) {
            $this->parseErrors[] = $fileInfo;

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
