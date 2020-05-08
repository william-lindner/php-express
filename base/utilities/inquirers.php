<?php

if (!function_exists('config')) {
    /**
     * Loads the configuration option based on an identifier passed.
     *
     * @param string $identifier
     * @param mixed  $default
     *
     * @return mixed
     */
    function config(string $identifier, $default = null)
    {
        return \Express\Configuration::get($identifier, $default);
    }
}

if (!function_exists('user')) {
    /**
     * Access information about the user of the site.
     *
     * @return mixed|null
     */
    function user()
    {
        return \Express\Container::retrieve('visitor');
    }
}