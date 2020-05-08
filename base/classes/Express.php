<?php

namespace Express;

use Closure;
use ReflectionException;
use Express\Interfaces\Middleware;
use Express\Http\Request;

class Express
{

    /**
     * The registered middleware for the Express server
     *
     * @var array[]
     */
    protected $middleware = [
        'before' => [],
        'after'  => [],
    ];

    /**
     * Builds the environment for the Express server to run.
     *
     * @param Request|null $request
     *
     * @constructor
     */
    public function __construct(?Request $request = null)
    {
        Configuration::setup();

        Container::store('session', new Session());

        if ($request !== null) {
            Container::store('request', $request);
            Container::store('visitor', new Visitor());
        }
    }

    /**
     * Register a middleware to run before routing.
     *
     * @param Closure|Middleware $middleware
     *
     * @return Express
     */
    public function before($middleware)
    {
        return $this->use($middleware, 'before');
    }

    /**
     * Adds middleware to the before and after registrations
     *
     * @param        $middleware
     * @param string $type
     *
     * @return Express
     */
    protected function use($middleware, string $type = 'before') : Express
    {
        if ($this->isValidMiddleware($middleware)) {
            $this->middleware[$type][] = $middleware;
        }

        return $this;
    }

    /**
     * Contains the conditions to validate middleware as valid
     *
     * @param $middleware
     *
     * @return boolean
     */
    protected function isValidMiddleware($middleware) : bool
    {
        return $middleware instanceof \Closure || is_string($middleware);
    }

    /**
     * Register a middleware after routing is completed.
     *
     * @param $middleware
     *
     * @return Express
     */
    public function after($middleware)
    {
        return $this->use($middleware, 'after');
    }

    /**
     * Gets everything ready for a great cup of tea.
     *
     * @return void
     * @throws Exception\RouteNotFound
     * @throws ReflectionException
     */
    public function run() : void
    {
        if (!$request = Container::retrieve('request')) {
            // @todo - cmd
            return;
        }

        $this->middleware('before');

        Router::direct($request);
    }

    /**
     * Executes the middle ware of a specified type
     *
     * @param string $type
     *
     * @return void
     */
    protected function middleware(string $type) : void
    {
        foreach ($this->middleware[$type] as $middleware) {
            if ($middleware instanceof \Closure) {

                // @todo InjectionClosure
                $middleware($this->request, $this->visitor);
                continue;
            }

            if ($middleware instanceof Middleware) {

                // @todo InjectionMethod
                $middleware::execute($this->request, $this->visitor);
            }
        }
    }

    public function __debugInfo()
    {
        return [
            'middleware' => $this->middleware,
            'request'    => Container::retrieve('request'),
            'visitor'    => Container::retrieve('visitor'),
            'session'    => Container::retrieve('session'),
        ];
    }

    public function __destruct()
    {
        $this->middleware('after');
    }
}
