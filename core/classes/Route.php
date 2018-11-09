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
        static::register('GET', $path, $callback);
    }

    public static function post($path, Closure $callback)
    {
        static::register('POST', $path, $callback);
    }

    public static function patch($path, Closure $callback)
    {
        static::register('PATCH', $path, $callback);
    }

    /**
     * Directs the incoming request based on registered routes.
     * @param string $method
     * @param string $request
     */
    public static function direct($method, $request)
    {

        require 'routes.php';

        $request    = static::strip($request);
        $executable = isset(static::$routes[$method][$request]) && static::$routes[$method][$request] instanceof Closure;

        if ($executable) {
            static::$routes[$method][$request]();
        }
    }

    /**
     * Registers a route with the routes array.
     *
     * @param string $method
     * @param string $path
     * @param Closure $callback
     * @return void
     */
    protected static function register($method, $path, Closure $callback)
    {
        $path = static::strip($path);

        static::$routes[$method][$path] = $callback;
    }

    /**
     * Removes the forward slashes at beginning and end of URI
     *
     * @param string $path
     * @return string
     */
    protected static function strip($path)
    {
        return trim($path, '/');
    }
}
