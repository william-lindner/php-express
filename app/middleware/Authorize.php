<?php

namespace App\Middleware;

use Express\Authorization;
use Express\Interfaces\Gatekeeper;
use Express\Interfaces\Middleware;
use Express\Request;
use Express\Traits\ViewHandler;
use Express\Visitor;

class Authorize extends Authorization implements Middleware, Gatekeeper
{

    use ViewHandler;

    public static function run(Request $request, Visitor $visitor)
    {
        if (!static::check($request)) {
            static::deny($request);
        }
    }

    public static function deny(Request $request)
    {
        $instance = new static;
        $instance->loadPageNotFound();
    }
}
