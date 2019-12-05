<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use Composer\Autoload\ClassLoader;
use Exception;
use ReflectionClass;
use RuntimeException;

final class Helper
{
    /**
     * @var string
     */
    private static $projectRootDirectory;

    /**
     * @return array[]
     * @throws Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getComposerJsonDecoded(string $path = null): array
    {
        $path     = $path ?? self::getProjectRootDirectory() . '/composer.json';
        $contents = (string)\file_get_contents($path);
        if ($contents === '') {
            throw new RuntimeException('composer.json is empty');
        }
        $decoded = \json_decode($contents, true);
        if (\json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Failed loading composer.json: ' . \json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Get the absolute path to the root of the current project.
     *
     * It does this by working from the Composer autoloader which we know will be in a certain place in `vendor`
     *
     * @throws Exception
     */
    public static function getProjectRootDirectory(): string
    {
        if (self::$projectRootDirectory === null) {
            $reflection                 = new ReflectionClass(ClassLoader::class);
            self::$projectRootDirectory = \dirname((string)$reflection->getFileName(), 3);
        }

        return self::$projectRootDirectory;
    }
}
