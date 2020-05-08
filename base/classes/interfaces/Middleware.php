<?php

namespace Express\Interfaces;

use Express\Http\Request;
use Express\Visitor;

interface Middleware
{
    /**
     * Runs the middleware
     *
     * @param Request $request
     * @param Visitor $visitor
     */
    public static function run(Request $request, Visitor $visitor);
}
