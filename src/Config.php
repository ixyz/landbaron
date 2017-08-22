<?php namespace Ixyz\Landbaron;

use Ixyz\Landbaron\IO\Path;

class Config
{
    private $directory = '';
    private $config = [];

    /**
     * @param string $directory
     * @return void
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string $name
     * @param string $key
     * @return mixed
     */
    public function get($name, $key = '')
    {
        if ($key === '') {
            return $this->config($name);
        } else {
            $segments = explode('.', $key);
            $value = $this->config($name);

            for ($i = 0; $i < count($segments); ++$i) {
                $value = $value[$segments[$i]];
            }

            return $value;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function config($name)
    {
        if (!isset($this->config[$name])) {
            $this->config[$name] = require(Path::resolve($this->directory, 'config', $name.'.php'));
        }

        return $this->config[$name];
    }
}
