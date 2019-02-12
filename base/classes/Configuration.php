<?php

namespace Express;

class Configuration
{

    protected static $ini    = null;
    protected static $config = [];

    protected static function load($file)
    {

        $path   = $_SERVER['DOCUMENT_ROOT'] . "/../config/{$file}.php";
        $exists = file_exists($path);

        if (!isset(self::$config[$file]) && $exists) {
            self::$config[$file] = require $path;
        }

    }

    /**
     * Loads the settings from the ini file
     * @return array
     */
    public static function ini()
    {
        // TODO: write out try catch
        self::$ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../.ini', true);

        return self::$ini;
    }

    /**
     * Loads the guards for the ini configuration file
     * @return array
     */
    public static function guards()
    {
        if (!isset(self::$config['ini'])) {
            self::$config['ini'] = require $_SERVER['DOCUMENT_ROOT'] . '/../config/ini.php';
        }

        return isset(self::$config['ini']['protected']) ? self::$config['ini']['protected'] : [];
    }

    /**
     * Loads the list of classes that are permitted access to ini information regardless of guard
     * @return array
     */
    public static function allowed()
    {
        if (!isset(self::$config['Express'])) {
            self::$$config['Express'] = require $_SERVER['DOCUMENT_ROOT'] . '/../config/ini.php';
        }

        return isset(self::$config['teapot']['protected']) ? self::$config['teapot']['protected'] : [];
    }
}
