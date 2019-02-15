<?php

namespace Express;

class View
{
    protected static $instance;

    protected $data = [];

    protected function __construct(array $data = [])
    {

    }

    public function instance(Request $request, string $viewPath, array $data = [])
    {
        if (isset(static::$instance)) {
            static::$instance->add($data);
            return static::$instance;
        }

        static::$instance = new static($data);

        return static::$instance;
    }

    public function load(string $viewPath)
    {

    }

    public function add(\Closure $function)
    {
        //
    }

    public function extract()
    {

    }
}
