<?php namespace Ixyz\Landbaron\Brand;

class Option
{
    private $args = [];

    public static function instance($args)
    {
        return new static($args);
    }

    private function __construct($args)
    {

        $this->args = $args;
    }

    public function exist($option)
    {
        $match = false;

        foreach ($this->args as $key => $arg) {
            if ($option === $key) {
                $match = true;
            }
        }

        return $match;
    }
}
