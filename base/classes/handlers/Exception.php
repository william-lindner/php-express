<?php

namespace Express\Handlers;

class Exception
{
    /**
     * Registers the Exception handler within the class to output all exceptions
     * in Express format.
     * @return void
     */
    public static function register()
    {
        set_exception_handler(['\\Express\\Handlers\\Exception', 'handler']);
    }

    /**
     * Handles exceptions in a quiet way for the framework.
     * @return void
     */
    public static function handler($e)
    {
        if (strtolower(config('server.environment', 'prod')) !== 'prod') {
            $code    = $e->getCode ?? 404;
            $message = $e->getMessage() ?? 'Unknown';
            dd([
                'Exception' => "[{$code}] {$message}",
                'Trace'     => debug_backtrace()[0]['args'],
            ]);
        }

        // send a 400 by default
    }
}
