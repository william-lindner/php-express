<?php

namespace Teapot;

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

        dd(self::$ini);
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
     *
     */
    public static function allowed()
    {
        if (!isset(self::$config['teapot'])) {
            self::$$config['teapot'] = require $_SERVER['DOCUMENT_ROOT'] . '/../config/ini.php';
        }

        return isset(self::$config['teapot']['protected']) ? self::$config['teapot']['protected'] : [];
    }
}
