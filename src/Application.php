<?php namespace Ixyz\Landbaron;

use App\Router;
use Ixyz\Landbaron\App\Autoload;
use Ixyz\Landbaron\Response\Json;
use Ixyz\Landbaron\Response\View;
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

    public function execute()
    {
        $namespace = $this->config->get('app', 'namespace.controller');
        $paths = Path::absolutes($this->directory, $this->config->get('view', 'paths'));
        $compiled = Path::resolve($this->directory, $this->config->get('view', 'compiled'));

        $this->router = Router::register(Route::instance());
        $this->functionThreading($namespace, $paths, $compiled);
        $this->actionThreading($namespace, $paths, $compiled);
        $this->filterThreading($namespace, $paths, $compiled);
        $this->templateThreading($namespace, $paths, $compiled);
        $this->configThreading();
    }

    private function configThreading()
    {
        // Theme support
        $themeSupports = $this->config->get('theme');
        foreach ($themeSupports as $themeSupport) {
            call_user_func_array('add_theme_support', $themeSupport);
        }

        // Remove action hooks
        $actions = $this->config->get('hooks', 'action');
        foreach ($actions as $action) {
            call_user_func_array('remove_action', $action);
        }

        // Remove filter hooks
        $filters = $this->config->get('hooks', 'filter');
        foreach ($filters as $filter) {
            call_user_func_array('remove_filter', $filter);
        }

        // Mime types allowed for upload
        add_filter('upload_mimes', function ($type) {
            $mime = $this->config->get('mimes');

            return array_merge($type, $mime);
        });
    }

    private function functionThreading($namespace, $paths, $compiled)
    {
        $threads = $this->router->getFunctions();

        foreach ($threads as $thread) {
            $callback = $thread->getCallback($namespace, $paths, $compiled);
            $args = $thread->getParams();
            call_user_func_array('add_action', array_merge([$name, $callback], $args));
        }
    }

    private function actionThreading($namespace, $paths, $compiled)
    {
        $actions = $this->router->getActions();

        foreach ($actions as $name => $threads) {
            foreach ($threads as $thread) {
                $callback = $thread->getCallback($namespace, $paths, $compiled);
                $args = $thread->getParams();
                call_user_func_array('add_action', array_merge([$name, $callback], $args));
            }
        }
    }

    private function filterThreading($namespace, $paths, $compiled)
    {
        $filters = $this->router->getFilters();

        foreach ($filters as $name => $threads) {
            foreach ($threads as $thread) {
                $callback = $thread->getCallback($namespace, $paths, $compiled);
                $args = $thread->getParams();
                call_user_func_array('add_action', array_merge([$name, $callback], $args));
            }
        }
    }

    private function templateThreading($namespace, $paths, $compiled)
    {
        $threads = $this->router->getTemplates();

        add_filter('template_include', function ($template) use ($namespace, $paths, $compiled, $threads) {
            foreach ($threads as $name => $thread) {
                if (self::detect($name)) {
                    $callback = $thread->getCallback($namespace, $paths, $compiled);
                    $args = $thread->getParams();
                    $response = call_user_func_array($callback, $args);

                    if ($response instanceof View) {
                        $response->render();
                        return $template;
                    }

                    if ($response instanceof Json) {
                        $response->render();
                        return $template;
                    }
                }
            }

            return $template;
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
