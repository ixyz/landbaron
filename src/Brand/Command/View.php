<?php namespace Ixyz\Landbaron\Brand\Command;

use \Exception;
use Ixyz\Landbaron\Brand\Command;
use Ixyz\Landbaron\Config;
use Ixyz\Landbaron\IO\Path;

class View
{
    private $appDir = '';
    private $command = null;

    public static function instance($command, $appDir)
    {
        return new static($command, $appDir);
    }

    private function __construct($command, $appDir)
    {
        $this->command = $command;
        $this->appDir = $appDir;
    }

    public function clear()
    {
        $config = new Config($this->appDir);

        try {
            $path = Path::resolve($this->appDir, $config->get('view', 'compiled'));
            $files = Path::resolve($path, '*.php');

            foreach (glob($files) as $file) {
                unlink($file);
            }

            echo 'Cleared compiled views.';
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
