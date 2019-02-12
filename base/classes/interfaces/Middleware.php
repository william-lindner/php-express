<?php

namespace Express\Interfaces;

use Express\Request;
use Express\Visitor;

interface Middleware
{
    /**
     * Runs the middleware
     */
    public static function execute(Request $request, Visitor $visitor);
}
