<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Helper;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use RuntimeException;
use Throwable;

use function current;
use function dirname;
use function explode;
use function preg_match;
use function preg_replace;
use function realpath;
use function rtrim;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strlen;
use function strpos;
use function trim;

final class LinksChecker
{
    /**
     * @throws Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function main(string $projectRootDirectory = null): int
    {
        $return               = 0;
        $projectRootDirectory = $projectRootDirectory ?? Helper::getProjectRootDirectory();
        $files                = static::getFiles($projectRootDirectory);
        foreach ($files as $file) {
            $relativeFile = str_replace([$projectRootDirectory], [''], $file)[0];
            $title        = "\n{$relativeFile}\n" . str_repeat('-', strlen($relativeFile[0])) . "\n";
            $errors       = [];
            $links        = static::getLinks($file);
            foreach ($links as $link) {
                static::checkLink($projectRootDirectory, $link, $file, $errors, $return);
            }
            if ([] !== $errors) {
                echo $title . implode('', $errors);
            }
        }

        return $return;
    }

    /**
     * @return string[]
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getFiles(string $projectRootDirectory): array
    {
        $files   = self::getDocsFiles($projectRootDirectory);
        $files[] = self::getMainReadme($projectRootDirectory);

        return $files;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return string[]
     */
    private static function getDocsFiles(string $projectRootDirectory): array
    {
        $files = [];
        $dir   = $projectRootDirectory . '/docs';
        if (!is_dir($dir)) {
            return $files;
        }
        $directory = new RecursiveDirectoryIterator($dir);
        $recursive = new RecursiveIteratorIterator($directory);
        $regex     = new RegexIterator(
            $recursive,
            '/^.+\.md/i',
            RecursiveRegexIterator::GET_MATCH
        );
        foreach ($regex as $file) {
            if ($file[0] !== '') {
                $files[] = $file[0];
            }
        }

        return $files;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getMainReadme(string $projectRootDirectory): string
    {
        $path = $projectRootDirectory . '/README.md';
        if (!is_file($path)) {
            throw new RuntimeException(
                "\n\nYou have no README.md file in your project"
                . "\n\nAs the bear minimum you need to have this file to pass QA"
            );
        }

        return $path;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return array<array<string>>
     */
    private static function getLinks(string $file): array
    {
        $links    = [];
        $contents = (string)file_get_contents($file);
        if (
            preg_match_all(
                '/\[(.+?)\].*?\((.+?)\)/',
                $contents,
                $matches,
                PREG_SET_ORDER
            ) !== false
        ) {
            $links = array_merge($links, $matches);
        }

        return $links;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param string[] $link
     * @param string[] $errors
     */
    private static function checkLink(
        string $projectRootDirectory,
        array $link,
        string $file,
        array &$errors,
        int &$return
    ): void {
        $path = trim($link[2]);
        if (strpos($path, '#') === 0) {
            return;
        }
        if (preg_match('%^(http|//)%', $path) === 1) {
            self::validateHttpLink($link, $errors, $return);

            return;
        }

        $path  = current(explode('#', $path, 2));
        $start = rtrim($projectRootDirectory, '/');
        if ($path[0] !== '/' || strpos($path, './') === 0) {
            $relativeSubdirs = preg_replace(
                '%^' . $projectRootDirectory . '%',
                '',
                dirname($file)
            );
            if ($relativeSubdirs !== null) {
                $start .= '/' . rtrim($relativeSubdirs, '/');
            }
        }
        $realpath = realpath($start . '/' . $path);
        if ($realpath === false) {
            $errors[] = sprintf("\nBad link for \"%s\" to \"%s\"\n", $link[1], $link[2]);
            $return   = 1;
        }
    }

    /**
     * @param string[] $link
     * @param string[] $errors
     */
    private static function validateHttpLink(array $link, array &$errors, int &$return): void
    {
        list(, $anchor, $href) = $link;
        static $checked        = [];
        $hashPos               = (int)strpos($href, '#');
        if ($hashPos > 0) {
            $href = substr($href, 0, $hashPos);
        }
        if (isset($checked[$href])) {
            return;
        }
        $checked[$href] = true;
        //$start          = microtime(true);
        //fwrite(STDERR, "\n".'Validating link: '.$href);
        $context = stream_context_create([
            'http' => [
                'method'           => 'HEAD',
                'protocol_version' => 1.1,
                'header'           => [
                    'Connection: close',
                ],
            ],
        ]);
        $result  = null;
        try {
            $headers = get_headers($href, 0, $context);
            if ($headers === false) {
                throw new RuntimeException('Failed getting headers for href ' . $href);
            }
            foreach ($headers as $header) {
                if (strpos($header, ' 200 ') !== false) {
                    //$time = round(microtime(true) - $start, 2);
                    //fwrite(STDERR, "\n".'OK ('.$time.' seconds): '.$href);

                    return;
                }
            }
        } catch (Throwable $e) {
            throw new RuntimeException('Unexpected error ' . $e->getMessage(), $e->getCode(), $e);
        }

        $errors[] = sprintf(
            "\nBad link for \"%s\" to \"%s\"\nresult: %s\n",
            $anchor,
            $href,
            var_export($result, true)
        );
        $return   = 1;
        //$time     = round(microtime(true) - $start, 2);
        //fwrite(STDERR, "\n".'Failed ('.$time.' seconds): '.$href);
    }
}
