<?php

use Teapot\Configuration;

/**
 * Loads the configuration option based on an identifier passed.
 * @param string
 * @return any | bool
 */
function config(string $identifier)
{
    return Configuration::get($identifier);
}

/**
 * Accesses the session super global in easy to use function.
 *
 */
function session(string $dotPattern)
{
    // return dotseek($dotPattern, $_SESSION);
}

/**
 * Access information about the user of the site.
 */
function user(string $key = null)
{
    if (!$key) {
        return $_SESSION['user'];
    }
    return $_SESSION['user'][$key] ?? null;
}
