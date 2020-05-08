<?php

namespace Express;

use Express\Diagnostic\Memory;

class Diagnostic
{
    /**
     * Get the current memory usage
     *
     * @return Memory
     */
    public static function memory() : Memory
    {
        return new Memory();
    }
}