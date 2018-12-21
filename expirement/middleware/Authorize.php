<?php

namespace App\Middleware;

use Teapot\Authorization;
use Teapot\Interfaces\Gatekeeper;
use Teapot\Interfaces\Middleware;
use Teapot\Request;
use Teapot\Visitor;

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
        ob_clean();
        http_response_code(404);
        view('system/404');
        die;
    }
}
