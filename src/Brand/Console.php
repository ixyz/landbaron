<?php namespace Ixyz\Landbaron\Brand;

use Ixyz\Landbaron\Brand\Command;
use Ixyz\Landbaron\Brand\Command\Make;
use Ixyz\Landbaron\Brand\Command\Version;
use Ixyz\Landbaron\Brand\Command\View;
use Ixyz\Landbaron\Brand\Option;

class Console
{
    private $currentDir = '';
    private $appDir = '';
    private $params = [
        'short' => [ 'v' ],
        'long'  => [ 'version' ]
    ];
    private $options = [];

    public static function instance($args, $appDir)
    {
        return new static($args, $appDir);
    }

    private function __construct($args, $appDir)
    {
        $this->args = $args;
        $this->appDir = $appDir;
        $this->currentDir = __DIR__;
        $this->options = getopt(implode('', $this->params['short']), $this->params['long']);
    }

    public function execute()
    {
        $command = Command::instance($this->args, $this->options);
        $option = Option::instance($this->options);

        if ($command->exist('make:controller')) {
            Make::instance($command, $this->appDir, $this->currentDir)->controller();
            return;
        }

        if ($command->exist('make:model')) {
            Make::instance($command, $this->appDir, $this->currentDir)->model();
            return;
        }

        if ($command->exist('view:clear')) {
            View::instance($command, $this->appDir)->clear();
            return;
        }
    }
}
