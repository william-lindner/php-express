<?php

namespace Teapot;

use Teapot\Session;

final class Teapot
{
    protected static $setup  = false;
    protected static $config = null;

    // holds the keys that cannot be accessed in the config
    protected static $guard = [];

    public function __construct()
    {

        if (self::$setup) {
            return;
        }

        // Note: not certain about the include path takeover
        set_include_path($_SERVER['DOCUMENT_ROOT'] . '/../app');
        ini_set('include_path', $_SERVER['DOCUMENT_ROOT'] . '/../app');
        Exception::register();
        Session::start();

        self::$config = Configuration::load();
        self::$guard  = Configuration::guards();

        require 'routes.php';
        self::$setup = true;
    }

    /**
     * Loads the configuration setting from the protected property.
     * TODO: Will need to respect guards
     * @param string $key
     */
    public static function config($key)
    {

        if (!in_array($key, self::$guard) && isset(self::$config[$key])) {
            return self::$config[$key];
        }

        return false;
    }

}
