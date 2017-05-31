<?php namespace Ixyz\Landbaron\Core;

use Ixyz\Landbaron\Core\Thread;

class Route
{
    private $functions = [];
    private $actions = [];
    private $filters = [];
    private $templates = [];

    /**
     * @return Route
     */
    public static function instance()
    {
        return new static;
    }

    /**
     * @param string $route
     * @return void
     */
    public function function($route, ...$args)
    {
        $this->functions[] = Thread::instance($route, $args);
    }

    /**
     * @param string $name
     * @param string $route
     * @param mixed $args
     * @return void
     */
    public function action($name, $route, ...$args)
    {
        $this->actions[$name][] = Thread::instance($route, $args);
    }

    /**
     * @param string $name
     * @param string $route
     * @param mixed $args
     * @return void
     */
    public function filter($name, $route, ...$args)
    {
        $this->filter[$name][] = Thread::instance($route, $args);
    }

    /**
     * @param string $name
     * @param string $route
     * @return void
     */
    public function template($name, $route, ...$args)
    {
        $this->templates[$name] = Thread::instance($route, $args);
    }

    /**
     * @return Thread[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @return Thread[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return Thread[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return Thread[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
