<?php namespace Ixyz\Landbaron\Core\Response;

use Ixyz\Landbaron\Laravel\Blade;

class View
{
    private $resource = '';
    private $cache = '';
    private $views = [];
    private $values = [];

    /**
     * @param string $resource
     * @param string $cache
     * @param string $view
     * @param string $values
     * @return View
     */
    public static function instance($resource, $cache, $views, $values)
    {
        return new static($resource, $cache, $views, $values);
    }

    /**
     * @param string $resource
     * @param string $cache
     * @param string $views
     * @param string $values
     * @return void
     */
    private function __construct($resource, $cache, $views, $values)
    {
        $this->resource = $resource;
        $this->cache = $cache;
        $this->views = $views;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function render()
    {
        $blade = Blade::instance($this->resource, $this->cache);

        echo $blade->view()->make($this->views, $this->values)->render();
    }

    /**
     * @return mixed[]
     */
    public function invoke()
    {
        return [$this, 'render'];
    }
}
