<?php

namespace Express;

class Instance
{
    protected static $instance;

    protected $data = [];

    public function __construct($data)
    {

    }

    /**
     * Creates a singleton for views only
     */
    public static function view($data)
    {
        if (isset(static::$instance)) {
            $instance->add($data);
            return $instance;
        }

        $instance = new static($data);
    }

    public function add(\Closure $function)
    {
        //
    }

    public function extract()
    {

    }
}
