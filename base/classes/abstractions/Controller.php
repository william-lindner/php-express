<?php

namespace Express\Abstractions;

use Express\Container;
use Express\Http\Response;

abstract class Controller
{

    /**
     * @var Response
     */
    private $response;

    public function __construct()
    {
        if (!Container::retrieve('response')) {
            Container::store('response', new Response());
        }
    }

    /**
     * Redirects to a uri pattern (for internal use relative)
     *
     * @param string uri
     */
    protected function redirect(string $uri) : void
    {
        // todo : implement redirect system
    }

    /**
     * @return Response
     */
    protected function response() : Response
    {
        return Container::retrieve('response');
    }
}
