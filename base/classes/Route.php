<?php

namespace Express;

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
     * @param any $handler
     * @return void
     */
    public static function get($path, $handler)
    {
        static::register('GET', $path, $handler);
    }

    /**
     * Helper function to register a POST request.
     *
     * @param string $path
     * @param any $handler
     * @return void
     */
    public static function post($path, $handler)
    {
        static::register('POST', $path, $handler);
    }

    /**
     * Helper function to register a PATCH request.
     *
     * @param string $path
     * @param any $handler
     * @return void
     */
    public static function patch($path, $handler)
    {
        static::register('PATCH', $path, $handler);
    }

    /**
     * Directs the incoming request based on registered routes.
     * @param string $method
     * @param string $request
     */
    public static function direct(Request $request)
    {

        require __BASEDIR__ . '/app/routes.php';
        $key = static::stripSlashes($request->uri);

        if (!isset(static::$routes[$request->method][$key])) {
            return;
        }

        static::boot(static::$routes[$request->method][$key], $request);
    }

    /**
     * Runs a route given the specified request
     * @param any $policy
     */
    protected static function boot($policy, Request $request)
    {
        // todo: add integrity checks in function loadController
        switch (gettype($policy)) {
            case 'string':
                $command   = explode('@', $policy);
                $className = '\\App\\Controllers\\' . $command[0];
                (new $className($request))->{$command[1] ?? 'index'}();
                break;
            default:
                $policy();
        }
        exit;
    }

    /**
     * Loads the controller given an @method
     * @
     */
    protected static function loadController($command)
    {

    }

    /**
     * Registers a route with the routes array.
     *
     * @param string $method
     * @param string $path
     * @param closure $callback
     * @return void
     */
    protected static function register($method, $path, $handler)
    {
        $path = static::stripSlashes($path);

        static::$routes[$method][$path] = $handler;
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
