<?php namespace Ixyz\Landbaron\IO;

class Path
{
    private $path = '';

    /**
     * @param string $base
     * @param string ...$args
     * @return string
     */
    public static function resolve(...$args)
    {
        $path = implode(DIRECTORY_SEPARATOR, $args);
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        return $path;
    }

    /**
     * @param string $base
     * @param string[] $paths
     * @return string
     */
    public static function absolutes($base, $paths)
    {
        $resolved = [];

        foreach ($paths as $path) {
            $resolved[] = self::resolve($base, $path);
        }

        return $resolved;
    }
}
