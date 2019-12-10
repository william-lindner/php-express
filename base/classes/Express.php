<?php

namespace Express;

use Express\Interfaces\Middleware;

final class Express
{
    /**
     * @var
     */
    protected $request;
    protected $visitor;

    protected $middleware = [
        'before' => [],
        'after'  => [],
    ];

    private static $instance = null;

    /**
     * Builds the environment for the Express server to run.
     *
     * @return Express
     */
    public function __construct(?Request $request = null)
    {
        if (self::$instance) {
            return self::$instance;
        }

        if (!defined('__BASEDIR__') && isset($_SERVER)) {
            define('__BASEDIR__', $_SERVER['DOCUMENT_ROOT'] . '/..');
        }

        Handlers\Exception::register();

        $this->request = $request;
        $session       = new Session();

        if ($this->request) {
            $this->visitor = new Visitor();
        }

        Configuration::setup(constant('__BASEDIR__') ?? __DIR__ . '/../');

        ini_set('display_errors', config('server.display_errors', 1));
        error_reporting(config('server.error_reporting', 1));
    }

    /**
     * Register a middleware to run before routing.
     *
     * @param  Closure|Middleware $middleware
     * @return Express
     */
    public function before($middleware)
    {
        return $this->use($middleware, 'before');
    }

    /**
     * Register a middleware after routing is completed.
     *
     * @return void
     */
    public function after($middleware)
    {
        return $this->use($middleware, 'after');
    }

    /**
     * Adds middleware to the before and after registrations
     *
     * @return void
     */
    protected function use($middleware, string $type = 'before')
    {
        if ($this->isValidMiddleware($middleware)) {
            $this->middleware[$type][] = $middleware;
        }

        return $this;
    }

    /**
     * Contains the conditions to validate middleware as valid
     *
     * @return boolean
     */
    protected function isValidMiddleware($middleware)
    {
        return $middleware instanceof \Closure
            || is_string($middleware)
            || is_object($middleware);
    }

    /**
     * Executes the middle ware of a specified type
     *
     * @return void
     */
    protected function middleware(string $type)
    {
        foreach ($this->middleware[$type] as $middleware) {
            if ($middleware instanceof \Closure) {
                $middleware($this->request, $this->visitor);
                continue;
            }

            if ($middleware instanceof Middleware || is_string($middleware)) {
                $middleware::execute($this->request, $this->visitor);
            }
        }
    }

    /**
     * Gets everything ready for a great cup of tea.
     *
     * @return void
     */
    public function run()
    {
        $this->middleware('before');

        Route::direct($this->request);
        return $this;
    }

    public function __debugInfo()
    {
        return [
            'middleware' => $this->middleware,
            'session'    => $this->session,
            'request'    => $this->request,
            'visitor'    => $this->visitor,
        ];
    }

    public function __destruct()
    {
        $this->middleware('after');
    }
}
