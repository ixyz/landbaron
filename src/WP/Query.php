<?php namespace Ixyz\Landbaron\WP;

use \WP_Query;

class Query
{
    private $query = null;

    /**
     * @param string[] $args
     * @return Query
     */
    public static function instance($args)
    {
        return new static($args);
    }

    /**
     * @param string[] $args
     * @return void
     */
    private function __construct($args)
    {
        $this->query = new WP_Query($args);
    }

    /**
     * @return WP_Query
     */
    public function wp()
    {
        return $this->query;
    }

    /**
     * @return Post[]
     */
    public function posts()
    {
        $posts = [];
        foreach ($this->query->posts as $post) {
            $posts[] = Post::instance($post);
        }

        return $posts;
    }

    /**
     * @return WP_Post
     */
    public function firstPost()
    {
        return Post::instance($this->query->posts[0]);
    }
}
