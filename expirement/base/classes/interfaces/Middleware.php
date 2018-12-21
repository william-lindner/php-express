<?php

namespace Teapot\Interfaces;

use Teapot\Request;
use Teapot\Visitor;

interface Middleware
{
    /**
     * Runs the middleware
     */
    public static function execute(Request $request, Visitor $visitor);
}
