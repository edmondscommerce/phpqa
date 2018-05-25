<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use Composer\Autoload\ClassLoader;

class Helper
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

    /**
     * @param string|null $path
     *
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getComposerJsonDecoded(string $path = null): array
    {
        $path    = $path ?? self::getProjectRootDirectory().'/composer.json';
        $decoded = \json_decode(\file_get_contents($path), true);
        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new \RuntimeException('Failed loading composer.json: '.\json_last_error_msg());
        }

        return $decoded;
    }
}
