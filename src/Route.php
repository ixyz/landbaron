<?php namespace Ixyz\Landbaron;

class Route
{
    private $funcs = [];
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
     * @param string $callback
     * @return void
     */
    public function func($callback, ...$args)
    {
        $this->funcs[] = Thread::instance($callback, $args);
    }

    /**
     * @param string $name
     * @param string $callback
     * @param mixed $args
     * @return void
     */
    public function action($name, $callback, ...$args)
    {
        $this->actions[$name][] = Thread::instance($callback, $args);
    }

    /**
     * @param string $name
     * @param string $callback
     * @param mixed $args
     * @return void
     */
    public function filter($name, $callback, ...$args)
    {
        $this->filters[$name][] = Thread::instance($callback, $args);
    }

    /**
     * @param string $name
     * @param string $callback
     * @return void
     */
    public function template($name, $callback, ...$args)
    {
        $this->templates[$name] = Thread::instance($callback, $args);
    }

    /**
     * @return Thread[]
     */
    public function getFunctions()
    {
        return $this->funcs;
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

    /**
     * @return Thread[]
     */
    public function getRedirects()
    {
        return $this->redirects;
    }
}
