<?php

namespace Express;

use \Closure;
use ReflectionException;
use Express\Http\Request;
use Express\Http\Routes;
use Express\Runners\ClassRunner;
use Express\Runners\ClosureRunner;
use Express\Exception\RouteNotFound;
use Express\Runners\Interfaces\Runnable;

class Router
{

    /**
     * Router constructor.
     *
     * @constructor
     */
    private function __construct()
    {
        // no constructor
    }

    /**
     * Helper function to register a GET request.
     *
     * @param string         $path
     * @param Closure|string $handler
     *
     * @return void
     */
    public static function get(string $path, $handler) : void
    {
        self::routes()->register('GET', $path, $handler);
    }

    /**
     * Helper to fetch the routes from the container
     *
     * @return Routes
     */
    private static function routes() : Routes
    {
        return Container::retrieve('routes');
    }

    /**
     * Helper function to register a POST request.
     *
     * @param string         $path
     * @param Closure|string $handler
     *
     * @return void
     */
    public static function post(string $path, $handler) : void
    {
        self::routes()->register('POST', $path, $handler);
    }

    /**
     * Helper function to register a PATCH request.
     *
     * @param string         $path
     * @param Closure|string $handler
     *
     * @return void
     */
    public static function patch($path, $handler) : void
    {
        self::routes()->register('PATCH', $path, $handler);
    }

    /**
     * Directs the incoming request based on registered routes.
     *
     * @param Request $request
     *
     * @throws RouteNotFound|ReflectionException
     */
    public static function direct(Request $request) : void
    {
        $router = new Routes();

        Container::store('routes', $router);

        $router->loadRoutes('web');

        if (!$handler = $router->match($request->route())) {
            throw new RouteNotFound('Not able to identify route pattern', 404);
        }

        $kind = (static::buildFrom($handler, $request->all()))->inject()->exec();

        if (is_string($kind)) {
            // should be response
            echo $kind;
        }
    }

    /**
     * Runs a route given the specified request
     *
     * @param mixed $handler
     * @param array $data
     *
     * @return Runnable
     */
    protected static function buildFrom($handler, array $data = []) : Runnable
    {
        if ($handler instanceof \Closure) {
            return new ClosureRunner($handler);
        }

        return new ClassRunner($handler);
    }
}
