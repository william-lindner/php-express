<?php

namespace Express\Interfaces;

use Express\Http\Request;

interface Gatekeeper
{
    /**
     * Denies access to the thing
     */
    public static function deny(Request $request);
}
