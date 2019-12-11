<?php

namespace Express\Interfaces;

use Express\Http\Request;
use Express\Visitor;

interface Middleware
{
    /**
     * Runs the middleware
     */
    public static function run(Request $request, Visitor $visitor);
}
