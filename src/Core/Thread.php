<?php namespace Ixyz\Landbaron\Core;

class Thread
{
    private $route = '';
    private $controller = '';
    private $action = '';
    private $args = [];

    /**
     * @param string $route
     * @return Thread|null
     */
    public static function instance($route, $args = [])
    {
        $segments = explode('@', $route);

        if (count($segments) === 2) {
            return new static($route, $segments[0], $segments[1], $args);
        } else {
            return null;
        }
    }

    /**
     * @param string $route
     * @param string $controller
     * @param string $action
     * @return void
     */
    private function __construct($route, $controller, $action, $args)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $args;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
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
