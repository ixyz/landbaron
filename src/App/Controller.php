<?php namespace Ixyz\Landbaron\App;

use Ixyz\Landbaron\App\Invoke;
use Ixyz\Landbaron\App\Response;
use Ixyz\Landbaron\Http\Input;
use Ixyz\Landbaron\WP\Post;

abstract class Controller
{
    private $id = null;
    private $post = null;
    private $input = null;
    private $resource = '';
    private $cache = '';

    /**
     * @param string $resource
     * @param string $cache
     * @return void
     */
    public function __construct($resource, $cache)
    {
        $this->resource = $resource;
        $this->cache = $cache;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function input()
    {
        if ($this->input === null) {
            $this->input = Input::instance();
        }

        return $this->input;
    }

    /**
     * @return mixed
     */
    protected function response()
    {
        return Response::instance($this->resource, $this->cache);
    }

    /**
     * @return void
     */
    protected function invoke()
    {
        return Invoke::instance($this->resource, $this->cache);
    }

    /**
     * @return int
     */
    protected function getID()
    {
        if ($this->id === null) {
            $this->id = get_the_ID();
        }

        return $this->id;
    }

    /**
     * @return Post
     */
    protected function getPost()
    {
        if ($this->post === null) {
            $this->post = Post::instanceById($this->getID());
        }

        return $this->post;
    }

    /**
     * @return WPPost
     */
    protected function getWPPost()
    {
        $post = $this->getPost();

        return $post->wp();
    }
}
