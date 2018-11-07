<?php

namespace Teapot;

class Configuration
{

    protected static $ini        = null;
    protected static $ini_config = null;

    /**
     * Loads the settings from the ini file
     * @return array
     */
    public static function load()
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
        if (!isset(self::$ini_config)) {
            self::$ini_config = require $_SERVER['DOCUMENT_ROOT'] . '/../config/ini.php';
        }

        return isset(self::$ini_config['protected']) ? self::$ini_config['protected'] : [];
    }
}
