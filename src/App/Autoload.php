<?php namespace Ixyz\Landbaron\App;

class Autoload
{
    private $directory = '';

    /**
     * @param string $directory
     * @return void
     */
    public static function instance($directory)
    {
        return new static($directory);
    }

    /**
     * @param string $directory
     * @return void
     */
    private function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return Autoload
     */
    public function register()
    {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * @return Autoload
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    /**
     * @param string $class
     * @return void
     */
    public function load($class)
    {
        $segments = explode('\\', ltrim($class, 'App\\'));
        $path = implode(DIRECTORY_SEPARATOR, $segments);
        $path = $this->directory.DIRECTORY_SEPARATOR.$path.'.php';

        if (is_readable($path)) {
            require $path;
        }
    }
}
