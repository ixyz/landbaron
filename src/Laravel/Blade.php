<?php namespace Ixyz\Landbaron\Laravel;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class Blade
{
    public $views;
    public $cache;
    private $container;
    private $factory;

    public static function instance($views = [], $cache, Dispatcher $events = null)
    {
        return new static($views, $cache, $events);
    }

    private function __construct($views = [], $cache, Dispatcher $events = null)
    {
        $this->container = new Container;
        $this->views = (array)$views;
        $this->cache = $cache;

        $this->registFilesystem();
        $this->registEvents($events);
        $this->registResolver();
        $this->registViewFinder();
    }

    public function view()
    {
        if($this->factory === null) {
            $this->factory = $this->registFactory();
        }

        return $this->factory;
    }

    private function registFilesystem()
    {
        $this->container->singleton('files', function () {
            return new Filesystem;
        });
    }

    private function registEvents($events)
    {
        $events = $events ?: new Dispatcher;
        $this->container->singleton('events', function () use ($events) {
            return $events;
        });
    }

    private function registResolver()
    {
        $_this = $this;
        $this->container->singleton('view.engine.resolver', function () use ($_this) {
            $resolver = new EngineResolver;
            $_this->registPhpEngine($resolver);
            $_this->registBladeEngine($resolver);

            return $resolver;
        });
    }

    private function registFactory()
    {
        $resolver = $this->container['view.engine.resolver'];
        $finder = $this->container['view.finder'];
        $factory = new Factory($resolver, $finder, $this->container['events']);
        $factory->setContainer($this->container);

        return $factory;
    }

    private function registViewFinder()
    {
        $_this = $this;
        $this->container->singleton('view.finder', function ($app) use ($_this) {
            return new FileViewFinder($app['files'], $_this->views);
        });
    }

    private function registPhpEngine($resolver)
    {
        $resolver->register('php', function () {
            return new PhpEngine;
        });
    }

    private function registBladeEngine($resolver)
    {
        $_this = $this;
        $app = $this->container;
        $this->container->singleton('blade.compiler', function ($app) use ($_this) {
            return new BladeCompiler($app['files'], $_this->cache);
        });

        $resolver->register('blade', function () use ($app) {
            return new CompilerEngine($app['blade.compiler'], $app['files']);
        });
    }
}
