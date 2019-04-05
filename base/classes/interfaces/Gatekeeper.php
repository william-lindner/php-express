<?php

namespace Express\Interfaces;

use Express\Request;

interface Gatekeeper
{
    /**
     * loads the view that should deny
     */
    public static function deny(Request $request);
}
