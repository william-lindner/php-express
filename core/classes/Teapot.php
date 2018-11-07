<?php

namespace Teapot;

use Teapot\Session;

final class Teapot
{
    protected static $config = null;

    public function __construct()
    {

        // Note: not certain about the include path takeover
        set_include_path($_SERVER['DOCUMENT_ROOT'] . '/../app');
        ini_set('include_path', $_SERVER['DOCUMENT_ROOT'] . '/../app');
        Exception::register();
        Session::start();

        self::$config = Configuration::load();

        require 'routes.php';
    }

    /**
     * Loads the configuration setting from the protected property.
     * TODO: Will need to respect guards
     */
    public static function config($key)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return false;
    }

}
