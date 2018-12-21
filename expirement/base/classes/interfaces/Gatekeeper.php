<?php

namespace Teapot\Interfaces;

use Teapot\Request;

interface Gatekeeper
{
    /**
     * loads the view that should deny
     */
    public static function deny(Request $request);
}
