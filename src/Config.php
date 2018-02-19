<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use Composer\Autoload\ClassLoader;

class Config
{
    private static $projectRootDirectory;

    /**
     * Get the absolute path to the root of the current project
     *
     * It does this by working from the Composer autoloader which we know will be in a certain place in `vendor`
     *
     * @return string
     * @throws \Exception
     */
    public static function getProjectRootDirectory(): string
    {
        try {
            if (null === self::$projectRootDirectory) {
                $reflection                 = new \ReflectionClass(ClassLoader::class);
                self::$projectRootDirectory = \dirname($reflection->getFileName(), 3);
            }

            return self::$projectRootDirectory;
        } catch (\Exception $e) {
            throw new \Exception(
                'Exception in '.__METHOD__.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
