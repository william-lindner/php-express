<?php

namespace Express;

class Exception
{
    /**
     * Registers the Exception handler within the class to output all exceptions in Express format.
     * @return void
     */
    public static function register()
    {
        set_exception_handler(['\\Express\\Exception', 'handler']);
    }

    /**
     * Handles exceptions in a quiet way for the framework.
     * @return void
     */
    public static function handler($e)
    {
        dump($e);
    }
}
