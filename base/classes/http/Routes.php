<?php

namespace Express\Http;

/*
|--------------------------------------------------------------------------
| Express Http Registry
|--------------------------------------------------------------------------
| Stores the routes that get registered for the server.
|
*/

use Closure;

class Routes
{
    /**
     * Local store of the request method
     *
     * @var string
     */
    private $scope;

    /**
     * The local routes array
     *
     * @var array
     */
    private $registry = [
        'web' => [
            'GET' => [],
        ]
    ];

    /**
     * Routes constructor.
     *
     * @constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Tags the scope of the request and registers the routes
     *
     * @param string $type
     */
    public function loadRoutes(string $type) : void
    {
        $path = __BASEDIR__ . '/app/routes/' . $type . '.php';

        if (file_exists($path)) {
            include_once $path;
        }
    }

    public function match(Route $route)
    {
        return $this->registry['web']['GET'][$route->uri()] ?? null;
    }


    /**
     * Registers a route with the routes array.
     *
     * @param string         $method
     * @param string         $path
     * @param Closure|string $handler
     *
     * @return void
     */
    public function register(string $method, string $path, $handler) : void
    {
        $this->registry['web'][$method][$path] = $handler;
    }
}