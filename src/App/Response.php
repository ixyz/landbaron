<?php namespace Ixyz\Landbaron\App;

use Ixyz\Landbaron\Response\Json;
use Ixyz\Landbaron\Response\View;

class Response
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
     * @param string $view
     * @param mixed $value
     * @return View
     */
    public function view($view, $value = [])
    {
        return View::instance($this->resource, $this->cache, $view, $value);
    }

    /**
     * @param mixed $value
     * @return Json
     */
    public function json($value)
    {
        return Json::instance($value);
    }
}
