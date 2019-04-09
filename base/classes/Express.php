<?php

namespace Express;

use Express\Interfaces\Middleware;

final class Express
{

    public static $baseDir;

    protected $request;
    protected $visitor;

    protected $middleware = [
        'before' => [],
        'after'  => [],
    ];

    /**
     * Builds the environment for the Express server to run.
     *
     * @return Express
     */
    public function __construct(Request $request, Visitor $visitor)
    {
        if (!defined('__BASEDIR__')) {
            define('__BASEDIR__', $_SERVER['DOCUMENT_ROOT'] . '/..');
        }

        self::$baseDir = __BASEDIR__;

        $this->request = $request;
        $this->visitor = $visitor;

        Exception::register();
        Session::start();
        Session::regenerate();

        Configuration::setup(Express::$baseDir);

        ini_set('display_errors', config('server.display_errors'));
        error_reporting(config('server.error_reporting'));
    }

    /**
     * Register a middleware before preparations.
     *
     * @return void
     */
    public function beforeRoute($middleware)
    {
        $this->useMiddleware('before', $middleware);
        return $this;
    }

    /**
     * Register a middleware after preparations.
     *
     * @return void
     */
    public function beforeEnd($middleware)
    {
        $this->useMiddleware('after', $middleware);
        return $this;
    }

    /**
     * Adds middleware to the before and after registrations
     *
     * @return void
     */
    protected function useMiddleware(string $type, $middleware)
    {
        if ($middleware instanceof \Closure || is_string($middleware) || is_object($middleware)) {
            $this->middleware[$type][] = $middleware;
        }
    }

    /**
     * Executes the middle ware of a specified type
     *
     * @return void
     */
    protected function runMiddleware(string $type)
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
        $this->runMiddleware('before');
        Route::direct($this->request);
        return $this;
    }

    public function __debugInfo()
    {
        return [
            'status'     => 'Express',
            'info'       => 'Come with me if you want to live.',
            'middleware' => $this->middleware,
            'baseDir'    => static::$baseDir,
        ];
    }

    public function __destruct()
    {
        $this->runMiddleware('after');
    }

}
