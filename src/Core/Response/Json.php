<?php namespace Ixyz\Landbaron\Core\Response;

class Json
{
    private $value = [];

    /**
     * @param mixed $value
     * @return Json
     */
    public static function instance($value)
    {
        return new static($value);
    }

    /**
     * @param mixed $value
     * @return void
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $resource
     * @param string $cache
     * @return void
     */
    public function render($encode = 'utf-8')
    {
        header("Content-Type: text/json; charset={$encode}");
        echo json_encode($this->value);
    }
}
