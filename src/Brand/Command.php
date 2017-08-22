<?php namespace Ixyz\Landbaron\Brand;

class Command
{
    private $args = [];
    private $command = '';
    private $option = '';

    public static function instance($args)
    {
        return new static($args);
    }

    private function __construct($args)
    {
        $this->args = $args;
    }

    public function exist($command)
    {
        foreach ($this->args as $arg) {
            preg_match("/([a-z0-9]+):([a-z0-9]+)/", $arg, $match);

            if ($arg === $command) {
                $this->command =  isset($match[1]) ? $match[1] : '';
                $this->option =  isset($match[2]) ? $match[2] : '';

                return true;
            }
        }

        return false;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getOption()
    {
        return $this->option;
    }
}
