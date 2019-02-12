<?php

namespace App\Middleware;

use App\Utilities\System;
use Express\Authorization;
use Express\Interfaces\Gatekeeper;
use Express\Interfaces\Middleware;
use Express\Request;
use Express\Visitor;

class Authorize extends Authorization implements Middleware, Gatekeeper
{
    public static function execute(Request $request, Visitor $visitor)
    {
        if (!static::check($request)) {
            static::deny($request);
        }
    }

    public static function deny(Request $request)
    {
        System::pageNotFound();
    }
}
