<?php namespace Ixyz\Landbaron\Core;

use App\Router;
use Ixyz\Landbaron\App\Autoload;
use Ixyz\Landbaron\Core\Response\Json;
use Ixyz\Landbaron\Core\Response\View;
use Ixyz\Landbaron\IO\Path;

class Application
{
    private $directory = '';
    private $config = null;
    private $router = null;

    public static function instance($directory)
    {
        return new static($directory);
    }

    private function __construct($directory)
    {
        $this->directory = $directory;
        $this->config = new Config($this->directory);
        $appPath = Path::resolve($this->directory, $this->config->get('app', 'path'));
        Autoload::instance($appPath)->register();
    }

    public function run()
    {
        $namespace = $this->config->get('app', 'namespace.controller');
        $paths = Path::absolutes($this->directory, $this->config->get('view', 'paths'));
        $compiled = Path::resolve($this->directory, $this->config->get('view', 'compiled'));

        $this->router = Router::register();
        $this->functionThreading($namespace, $paths, $compiled);
        $this->actionThreading($namespace, $paths, $compiled);
        $this->filterThreading($namespace, $paths, $compiled);
        $this->templateThreading($namespace, $paths, $compiled);
    }

    private function functionThreading($namespace, $paths, $compiled)
    {
        $threads = $this->router->getFunctions();

        foreach ($threads as $thread) {
            $controller = $namespace.$thread->getController();
            $action = $thread->getAction();
            $args = $thread->getParams();
            $instance = new $controller($paths, $compiled);
            call_user_func_array([$instance, $action], $args);
        }
    }

    private function actionThreading($namespace, $paths, $compiled)
    {
        $threads = $this->router->getActions();

        foreach ($threads as $name => $thread) {
            foreach ($thread as $function) {
                $controller = $namespace.$function->getController();
                $action = $function->getAction();
                $args = $function->getParams();
                $instance = new $controller($paths, $compiled);
                call_user_func_array('add_action', array_merge([$name, [$instance, $action]], $args));
            }
        }
    }

    private function filterThreading($namespace, $paths, $compiled)
    {
        $threads = $this->router->getFilters();

        foreach ($threads as $name => $thread) {
            foreach ($thread as $function) {
                $controller = $namespace.$function->getController();
                $action = $function->getAction();
                $args = $function->getParams();
                $instance = new $controller($paths, $compiled);
                call_user_func_array('add_filter', array_merge([$name, [$instance, $action]], $args));
            }
        }
    }

    private function templateThreading($namespace, $paths, $compiled)
    {
        $threads = $this->router->getTemplates();

        add_action('shutdown', function () use ($namespace, $paths, $compiled, $threads) {
            foreach ($threads as $name => $thread) {
                if (self::detect($name)) {
                    $controller = $namespace.$thread->getController();
                    $action = $thread->getAction();
                    $args = $thread->getParams();
                    $instance = new $controller($paths, $compiled);
                    $response = call_user_func_array([$instance, $action], $args);

                    if ($response instanceof View) {
                        $response->render();
                        return;
                    }

                    if ($response instanceof Json) {
                        $response->render();
                        return;
                    }
                }
            }
        });
    }

    /**
     * @param string $pageType
     * @return boolean
     */
    private static function detect($name)
    {
        switch ($name) {
            case 'index':
                return is_front_page() && is_home();
            case 'front_page':
                return is_front_page() && !is_home();
            case 'home':
                return is_home() && !is_front_page();
            default:
                $function = 'is_'.$name;
                return function_exists($function) && $function();
        }
    }
}
