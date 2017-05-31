<?php namespace Ixyz\Landbaron\App;

use Ixyz\Landbaron\Core\Invoke;
use Ixyz\Landbaron\Core\Response;
use Ixyz\Landbaron\WP\Post;

abstract class Controller
{
    private $id = null;
    private $post = null;
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
