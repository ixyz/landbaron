<?php namespace Ixyz\Landbaron\Response;

use Ixyz\Landbaron\Laravel\Blade;

class View
{
    private $resource = '';
    private $cache = '';
    private $view = '';
    private $values = [];

    /**
     * @param string $resource
     * @param string $cache
     * @param string $view
     * @param string $values
     * @return View
     */
    public static function instance($resource, $cache, $view, $values)
    {
        return new static($resource, $cache, $view, $values);
    }

    /**
     * @param string $resource
     * @param string $cache
     * @param string $view
     * @param mixed $values
     * @return void
     */
    private function __construct($resource, $cache, $view, $values)
    {
        $this->resource = $resource;
        $this->cache = $cache;
        $this->view = $view;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function render($render = true)
    {
        $blade = Blade::instance($this->resource, $this->cache);
        $renderd = $blade->view()->make($this->view, $this->values)->render();

        if ($render || $render === '') {
            echo $renderd;
        }

        return $renderd;
    }

    /**
     * @return array
     */
    public function invoke()
    {
        return [$this, 'render'];
    }
}
