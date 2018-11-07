<?php

namespace Teapot;

use Teapot\Session;
use Teapot\TPException;

final class Teapot
{
    protected static $setup  = false;
    protected static $config = null;

    // holds the keys that cannot be accessed in the config
    protected static $guard = [];

    // holds a list of classes allowed to access protected config keys
    protected static $allowed = [];

    public static $environment;
    public static $path;
    public static $method;

    public function __construct()
    {

        if (self::$setup) {
            return;
        }

        // Note: not certain about the include path takeover
        set_include_path($_SERVER['DOCUMENT_ROOT'] . '/../app');
        ini_set('include_path', $_SERVER['DOCUMENT_ROOT'] . '/../app');
        TPException::register();
        Session::start();

        self::$config = Configuration::load();
        self::$guard  = Configuration::guards();

        $ip                = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0;
        self::$environment = (($ip !== 0 && in_array($ip, ['127.0.0.1', '::1'])) || self::$config['env'] === 'local');

        self::$path   = $_SERVER['PATH_INFO'];
        self::$method = $_SERVER['REQUEST_METHOD'];

        // self::$allowed = require '/../';

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
