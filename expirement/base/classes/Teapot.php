<?php

namespace Teapot;

use Teapot\Interfaces\Middleware;

final class Teapot
{

    public static $baseDir;

    protected $request;
    protected $visitor;

    protected $middleware = [
        'before' => [],
        'after'  => [],
    ];

    /**
     * Make a little teapot, short and stout.
     *
     * @return Teapot
     */
    public function __construct(Request $request, Visitor $visitor)
    {
        self::$baseDir = defined('__BASEDIR__') ? __BASEDIR__ : __DIR__ . '/../..';

        // NOTE: A little not so DRY
        $this->request = $request;
        $this->visitor = $visitor;

        Exception::register();
        Configuration::setup(Teapot::$baseDir);
        Session::start();
        Session::regenerate();

        ini_set('display_errors', config('server.display_errors'));
    }

    /**
     * Register a middleware before preparations.
     *
     * @return void
     */
    public function beforeSteeping($middleware)
    {
        $this->useMiddleware('before', $middleware);
    }

    /**
     * Register a middleware after preparations.
     *
     * @return void
     */
    public function afterParty($middleware)
    {
        $this->useMiddleware('after', $middleware);
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
    public function steep()
    {
        $this->runMiddleware('before');
        return $this;
    }

    /**
     * Logs out a user.
     */
    public function endParty()
    {
        $this->runMiddleware('after');
        Session::stop();
        view('system/logout');
        die;
    }

    /**
     * Serves up the page content based on the request.
     */
    public function pour()
    {
        // removed code to allow prior prod builds to work
        return $this;
    }

    public function __debugInfo()
    {
        return [
            'status'  => 'Teapot',
            'info'    => 'Here is my handle. Here is my spout.',
            'baseDir' => static::$baseDir,
        ];
    }

}
