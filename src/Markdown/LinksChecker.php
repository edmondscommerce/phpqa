<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Helper;

class LinksChecker
{
    /**
     * @param string $projectRootDirectory
     *
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getFiles(string $projectRootDirectory): array
    {
        $files   = self::getDocsFiles($projectRootDirectory);
        $files[] = self::getMainReadme($projectRootDirectory);

        return $files;
    }

    /**
     * @param string $projectRootDirectory
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getMainReadme(string $projectRootDirectory): string
    {
        $path = $projectRootDirectory . '/README.md';
        if (!is_file($path)) {
            throw new \RuntimeException(
                "\n\nYou have no README.md file in your project"
                . "\n\nAs the bear minimum you need to have this file to pass QA"
            );
        }

        return $path;
    }

    /**
     * @param string $projectRootDirectory
     *
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getDocsFiles(string $projectRootDirectory): array
    {
        $files = [];
        $dir   = $projectRootDirectory . '/docs';
        if (!is_dir($dir)) {
            return $files;
        }
        $directory = new \RecursiveDirectoryIterator($dir);
        $recursive = new \RecursiveIteratorIterator($directory);
        $regex     = new \RegexIterator(
            $recursive,
            '/^.+\.md/i',
            \RecursiveRegexIterator::GET_MATCH
        );
        foreach ($regex as $file) {
            if ('' !== $file[0]) {
                $files[] = $file[0];
            }
        }

        return $files;
    }

    /**
     * @param string $file
     *
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getLinks(string $file): array
    {
        $links    = [];
        $contents = (string)file_get_contents($file);
        if (false !== preg_match_all(
                '/\[(.+?)\].*?\((.+?)\)/',
                $contents,
                $matches,
                PREG_SET_ORDER
            )) {
            $links = array_merge($links, $matches);
        }

        return $links;
    }

    /**
     * @param string $projectRootDirectory
     * @param array  $link
     * @param string $file
     * @param array  $errors
     * @param int    $return
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function checkLink(
        string $projectRootDirectory,
        array $link,
        string $file,
        array &$errors,
        int &$return
    ): void {
        $path = \trim($link[2]);
        if (0 === \strpos($path, '#')) {
            return;
        }
        if (1 === \preg_match('%^(http|//)%', $path)) {
            self::validateHttpLink($link, $errors, $return);

            return;
        }

        $path  = \current(\explode('#', $path, 2));
        $start = \rtrim($projectRootDirectory, '/');
        if ($path[0] !== '/' || 0 === \strpos($path, './')) {
            $relativeSubdirs = \preg_replace(
                '%^' . $projectRootDirectory . '%',
                '',
                \dirname($file)
            );
            $start           .= '/' . \rtrim($relativeSubdirs, '/');
        }
        $realpath = \realpath($start . '/' . $path);
        if (false === $realpath) {
            $errors[] = \sprintf("\nBad link for \"%s\" to \"%s\"\n", $link[1], $link[2]);
            $return   = 1;
        }
    }


    /**
     * @param string|null $projectRootDirectory
     *
     * @return int
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function main(string $projectRootDirectory = null): int
    {
        $return               = 0;
        $projectRootDirectory = $projectRootDirectory ?? Helper::getProjectRootDirectory();
        $files                = static::getFiles($projectRootDirectory);
        foreach ($files as $file) {
            $relativeFile = str_replace($projectRootDirectory, '', $file);
            $title        = "\n$relativeFile\n" . str_repeat('-', strlen($relativeFile)) . "\n";
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

    private static function validateHttpLink(array $link, array &$errors, int &$return): void
    {
        list(, $anchor, $href) = $link;
        static $checked = [];
        $hashPos = (int)strpos($href, '#');
        if ($hashPos > 0) {
            $href = substr($href, 0, $hashPos);
        }
        if (isset($checked[$href])) {
            return;
        }
        $checked[$href] = true;
        #$start          = microtime(true);
        #fwrite(STDERR, "\n".'Validating link: '.$href);
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
            if (false === $headers) {
                throw new \RuntimeException('Failed getting headers for href ' . $href);
            }
            foreach ($headers as $header) {
                if (false !== strpos($header, ' 200 ')) {
                    #$time = round(microtime(true) - $start, 2);
                    #fwrite(STDERR, "\n".'OK ('.$time.' seconds): '.$href);

                    return;
                }
            }
        } catch (\Throwable $e) {
        }

        $errors[] = \sprintf(
            "\nBad link for \"%s\" to \"%s\"\nresult: %s\n",
            $anchor,
            $href,
            var_export($result, true)
        );
        $return   = 1;
        #$time     = round(microtime(true) - $start, 2);
        #fwrite(STDERR, "\n".'Failed ('.$time.' seconds): '.$href);
    }
}
