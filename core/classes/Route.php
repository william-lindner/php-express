<?php

namespace Teapot;

use \Closure;

class Route
{

    protected static $routes = [
        'GET'   => [],
        'POST'  => [],
        'PATCH' => [],
    ];

    public static function get($path, Closure $callback)
    {
        static::$routes['GET'][$path] = $callback;
    }

    public static function post($path, Closure $callback)
    {

    }

    public static function patch($path, Closure $callback)
    {

    }

    public static function direct($method, $request)
    {

        $executable = isset(static::$routes[$method][$request]) && static::$routes[$method][$request] instanceof Closure;

        if ($executable) {
            static::$routes[$method][$request]();
        }
    }
}
