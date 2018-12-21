<?php

namespace Teapot\Brainstorms;

use \Closure;

class Route
{

    protected static $routes = [
        'GET'   => [],
        'POST'  => [],
        'PATCH' => [],
    ];

    /**
     * Helper function to register a GET request.
     *
     * @param string $path
     * @param closure $callback
     * @return void
     */
    public static function get($path, Closure $callback)
    {
        static::register('GET', $path, $callback);
    }

    /**
     * Helper function to register a POST request.
     *
     * @param string $path
     * @param closure $callback
     * @return void
     */
    public static function post($path, Closure $callback)
    {
        static::register('POST', $path, $callback);
    }

    /**
     * Helper function to register a PATCH request.
     *
     * @param string $path
     * @param closure $callback
     * @return void
     */
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
        $request = static::stripSlashes($request);

        if (isset(static::$routes[$method][$request])) {
            static::$routes[$method][$request]();
        }
    }

    /**
     * Registers a route with the routes array.
     *
     * @param string $method
     * @param string $path
     * @param closure $callback
     * @return void
     */
    protected static function register($method, $path, Closure $callback)
    {
        $path = static::stripSlashes($path);

        static::$routes[$method][$path] = $callback;
    }

    /**
     * Removes the forward slashes at beginning and end of URI
     *
     * @param string $path
     * @return string
     */
    protected static function stripSlashes($path)
    {
        return trim($path, '/');
    }
}
