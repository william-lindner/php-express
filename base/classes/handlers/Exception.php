<?php

namespace Express\Handlers;

class Exception
{
    /**
     * Cannot be instantiated externally
     */
    private function __construct()
    {
        //
    }

    /**
     * Registers the Exception handler within the class to output all exceptions
     * in Express format.
     *
     * @return void
     */
    public static function register() : void
    {
        set_exception_handler([__CLASS__, 'handler']);
    }

    /**
     * Handles exceptions in a quiet way for the framework.
     *
     * @return void
     */
    public static function handler($e) : void
    {
        if (config('server.environment', 'prod') !== 'prod') {
            $code    = $e->getCode ?? 500;
            $message = $e->getMessage() ?? 'Unknown';
            dd([
                'Exception' => "[{$code}] {$message}",
                'Trace'     => debug_backtrace()[0]['args'],
            ]);
        }

        // send a 400 by default
    }
}
