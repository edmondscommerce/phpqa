<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Config;

class LinksChecker
{
    /**
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getFiles(): array
    {
        $files   = self::getDocsFiles();
        $files[] = self::getMainReadme();

        return $files;
    }

    /**
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getMainReadme(): string
    {
        $path = Config::getProjectRootDirectory().'/README.md';
        if (!is_file($path)) {
            throw new \RuntimeException(
                "\n\nYou have no README.md file in your project"
                ."\n\nAs the bear minimum you need to have this file to pass QA"
            );
        }

        return $path;
    }

    /**
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function getDocsFiles(): array
    {
        $files = [];
        $dir   = Config::getProjectRootDirectory().'/docs';
        if (!is_dir($dir)) {
            $files;
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
     * @param array  $link
     * @param string $file
     * @param array  $errors
     * @param int    $return
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function checkLink(array $link, string $file, array &$errors, int &$return)
    {
        $path = $link[2];
        if (preg_match('/^(http|#)/', $path)) {
            return;
        }

        $path  = current(explode('#', $path, 2));
        $start = rtrim(Config::getProjectRootDirectory(), '/');
        if ($path[0] !== '/' || 0 === strpos($path, './')) {
            $relativeSubdirs = preg_replace(
                '%^'.Config::getProjectRootDirectory().'%',
                '',
                dirname($file)
            );
            $start           .= '/'.rtrim($relativeSubdirs, '/');
        }
        $realpath = realpath($start.'/'.$path);
        if (empty($realpath) || (!file_exists($realpath) && !is_dir($realpath))) {
            $errors[] = sprintf("\nBad link for \"%s\" to \"%s\"\n", $link[1], $link[2]);
            $return   = 1;
        }
    }


    /**
     * @return int
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function main()
    {
        $return = 0;
        $files  = static::getFiles();
        foreach ($files as $file) {
            $relativeFile = str_replace(Config::getProjectRootDirectory(), '', $file);
            $title        = "\n$relativeFile\n".str_repeat('-', strlen($relativeFile))."\n";
            if (!file_exists($file)) {
                echo "$title\nError - file $file does not exist\n";
                $return = 1;
                continue;
            }
            $errors = [];
            $links  = static::getLinks($file);
            foreach ($links as $link) {
                static::checkLink($link, $file, $errors, $return);
            }
            if (!empty($errors)) {
                echo $title.implode('', $errors);
            }
        }

        return $return;
    }
}
