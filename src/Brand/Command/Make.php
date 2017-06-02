<?php namespace Ixyz\Landbaron\Brand\Command;

use \Exception;
use Ixyz\Landbaron\Brand\Command;
use Ixyz\Landbaron\Config;
use Ixyz\Landbaron\IO\Path;

class Make
{
    private $currentDir = '';
    private $appDir = '';
    private $command = null;
    private $config = null;

    public static function instance($command, $appDir, $currentDir)
    {
        return new static($command, $appDir, $currentDir);
    }

    private function __construct($command, $appDir, $currentDir)
    {
        $this->command = $command;
        $this->appDir = $appDir;
        $this->currentDir = $currentDir;
        $this->config = new Config($this->appDir);
    }

    private function create($fqcn, $template)
    {
        try {
            if ($fqcn === '') {
                throw new Exception('Fully qualified class name was not found in argument.');
            }

            $segments = explode('\\', $fqcn);
            $class = array_pop($segments);
            $namespace = implode('\\', $segments);
            $path = Path::resolve($this->appDir, preg_replace('/^App/', $this->config->get('app', 'path'), $fqcn).'.php');

            $php = file_get_contents($template);
            if ($php === false) {
                throw new Exception("Failed to create {$class}.");
            }

            $php = str_replace('{NAMESPACE}', $namespace, $php);
            $php = str_replace('{CLASS}', $class, $php);

            $pathDir = dirname($path);
            if (!file_exists($pathDir)) {
                mkdir($pathDir, 0777, true);
            }

            if (file_exists($path)) {
                throw new Exception("{$class} already exists.");
            }

            $result = file_put_contents($path, $php);
            if (!$result) {
                throw new Exception("Failed to create {$class}.");
            }

            echo "Create {$class} successfully";
        } catch (Exeption $e) {
            die($e->getMessage());
        }
    }

    public function model()
    {
        $args = $this->command->getArgs();
        $fqcn = isset($args[2]) ? $this->config->get('app', 'namespace.model').$args[2] : '';
        $template = Path::resolve($this->currentDir, 'template/Model');
        $this->create($fqcn, $template);
    }

    public function controller()
    {
        $args = $this->command->getArgs();
        $fqcn = isset($args[2]) ? $this->config->get('app', 'namespace.controller').$args[2] : '';
        $template = Path::resolve($this->currentDir, 'template/Controller');
        $this->create($fqcn, $template);
    }
}
