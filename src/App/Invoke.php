<?php namespace Ixyz\Landbaron\App;

use Ixyz\Landbaron\Response\View;

class Invoke
{
    private $resource = null;
    private $cache = null;

    /**
     * @param Controller $controller
     * @return Response
     */
    public static function instance($resource, $cache)
    {
        return new static($resource, $cache);
    }

    /**
     * @param Controller $controller
     * @return void
     */
    private function __construct($resource, $cache)
    {
        $this->resource = $resource;
        $this->cache = $cache;
    }

    /**
     * @param string[] $view
     * @param mixed $value
     * @return mixed
     */
    public function view($view, $value = [])
    {
        return View::instance($this->resource, $this->cache, $view, $value)->invoke();
    }
}
