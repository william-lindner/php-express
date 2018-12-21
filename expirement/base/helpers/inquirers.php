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
function session(string $key)
{

    dd(explode('.', $key));

    return $_SESSION[$key];
}

/**
 * Access information about the user of the site.
 */
function user(string $key)
{
    return $_SESSION['user'][$key] ?? null;
}
