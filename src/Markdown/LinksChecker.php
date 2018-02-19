<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Markdown;

use EdmondsCommerce\PHPQA\Config;

class LinksChecker
{
    private static function getFiles(): array
    {
        $dir       = Config::getProjectRootDirectory().'/docs';
        $directory = new \RecursiveDirectoryIterator($dir);
        $recursive = new \RecursiveIteratorIterator($directory);
        $regex     = new \RegexIterator(
            $recursive,
            '/^.+\.md/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        $files = [Config::getProjectRootDirectory().'/README.md'];

        foreach ($regex as $file) {
            if (!empty($file[0])) {
                $files[] = $file[0];
            }
        }

        return $files;
    }

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

    public static function main()
    {
        $return = 0;
        $files  = static::getFiles();
        foreach ($files as $file) {
            if (!file_exists($file)) {
                echo "\nError - file $file does not exist\n";
            }
            $links = static::getLinks($file);
            foreach ($links as $link) {
                $path = $link[2];
                if (preg_match('/^(http|#)/', $path)) {
                    continue;
                }

                $path  = current(explode('#', $path, 2));
                $start = rtrim(Config::getProjectRootDirectory(), '/');
                if ($path[0] !== '/') {
                    $start .= '/'.rtrim(dirname($file), '/');
                }

                $realpath = realpath($start.'/'.$path);

                if (empty($realpath) || (!file_exists($realpath) && !is_dir($realpath))) {
                    printf("\n%s:\nBad link for \"%s\" to \"%s\"\n", $file, $link[1], $link[2]);
                    $return = 1;
                }
            }
        }

        return $return;
    }

}
