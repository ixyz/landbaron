<?php namespace Ixyz\Landbaron;

class Thread
{
    private $callback = null;
    private $controller = '';
    private $action = '';
    private $args = [];

    /**
     * @param string|callback $callback
     * @param mixed[] $args
     * @return Thread
     */
    public static function instance($callback, $args = [])
    {
        return new static($callback, $args);
    }

    /**
     * @param string|callback $callback
     * @param mixed[] $args
     * @return void
     */
    private function __construct($callback, $args)
    {
        if (is_string($callback)) {
            $segments = explode('@', $callback);

            if (count($segments) === 2) {
                $this->controller = $segments[0];
                $this->action = $segments[1];
                $this->params = $args;
            }
        } elseif (is_callable($callback)) {
            $this->callback = $callback;
            $this->params = $args;
        }
    }

    /**
     * @param string $namespace
     * @param string[] $paths
     * @param string $compiled
     * @return void
     */
    public function getCallback($namespace = '', $paths, $compiled)
    {
        if ($this->callback === null) {
            $controller = $namespace.$this->controller;
            $action = $this->action;
            $instance = new $controller($paths, $compiled);

            return [$instance, $this->action];
        } else {
            return $this->callback;
        }
    }

    /**
     * @return string
     */
    public function getController($namespace = '')
    {
        return $namespace.$this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }
}
