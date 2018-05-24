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
        $path = $projectRootDirectory.'/README.md';
        if (!is_file($path)) {
            throw new \RuntimeException(
                "\n\nYou have no README.md file in your project"
                ."\n\nAs the bear minimum you need to have this file to pass QA"
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
        $dir   = $projectRootDirectory.'/docs';
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
            if (!empty($file[0])) {
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
        $contents = file_get_contents($file);
        if (preg_match_all(
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
    ) {
        $path = \trim($link[2]);
        if (0 === \strpos($path, '#')) {
            return;
        }
        if (\preg_match('%^(http|//)%', $path)) {
            return self::validateHttpLink($link, $errors, $return);
        }

        $path  = \current(\explode('#', $path, 2));
        $start = \rtrim($projectRootDirectory, '/');
        if ($path[0] !== '/' || 0 === \strpos($path, './')) {
            $relativeSubdirs = \preg_replace(
                '%^'.$projectRootDirectory.'%',
                '',
                \dirname($file)
            );
            $start           .= '/'.\rtrim($relativeSubdirs, '/');
        }
        $realpath = \realpath($start.'/'.$path);
        if (empty($realpath) || (!\file_exists($realpath) && !\is_dir($realpath))) {
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
            $title        = "\n$relativeFile\n".str_repeat('-', strlen($relativeFile))."\n";
            $errors       = [];
            $links        = static::getLinks($file);
            foreach ($links as $link) {
                static::checkLink($projectRootDirectory, $link, $file, $errors, $return);
            }
            if (!empty($errors)) {
                echo $title.implode('', $errors);
            }
        }

        return $return;
    }

    private static function validateHttpLink(array $link, array &$errors, int &$return)
    {
        list(, $anchor, $link) = $link;
        $context = stream_context_create(['http' => ['method' => 'HEAD']]);
        try {
            $handle = fopen($link, 'rb', false, $context);
            $result = stream_get_meta_data($handle);
            foreach ($result['wrapper_data'] as $header) {
                if (false !== strpos($header, ' 200 ')) {
                    return;
                }
            }
        } catch (\Throwable $e) {
        }
        $errors[] = \sprintf("\nBad link for \"%s\" to \"%s\"\n", $anchor, $link);
        $return   = 1;

    }
}
