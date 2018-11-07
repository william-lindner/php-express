<?php

namespace Teapot;

class Exception
{
    /**
     * Registers the Exception handler within the class to output all exceptions in Teapot format.
     * @return void
     */
    public static function register()
    {
        set_exception_handler(['\Teapot\Exception', 'handler']);
    }

    public static function handler($e)
    {
        $trace = debug_backtrace();
        echo dump($e);
        return false;
    }
}
