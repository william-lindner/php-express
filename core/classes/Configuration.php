<?php

namespace Teapot;

class Configuration
{

    protected static $ini    = null;
    protected static $config = [];

    protected static function load($file)
    {

    }

    /**
     * Loads the settings from the ini file
     * @return array
     */
    public static function ini()
    {
        // TODO: write out try catch
        if (!isset(self::$ini)) {
            try {
                self::$ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../.ini', true);
            } catch (\Exception $e) {

            }
        }
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
