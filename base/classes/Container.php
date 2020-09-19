<?php

namespace Express;

/*
|--------------------------------------------------------------------------
| Express Container
|--------------------------------------------------------------------------
| The container provides a location of shared state between classes.
| It was created to reduce the memory needs of each individual
| object.
|
*/

class Container
{
    /**
     * The mem store of contents within the shared container
     *
     * @var array
     */
    private static $contents = [];

    private function __construct()
    {
        //
    }

    /**
     * Adds something to the container, if it doesn't already exist
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function store(string $key, $value)
    {
        if (!isset(self::$contents[$key])) {
            self::$contents[$key] = $value;
        }

        return self::$contents[$key];
    }

    /**
     * Fetches the item from the container, if it exists
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public static function retrieve(string $key)
    {
        return self::$contents[$key] ?? null;
    }
}