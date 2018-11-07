<?php

namespace Teapot;

class Configuration
{

    protected static $ini_settings = [];

    public static function load()
    {
        // TODO: Add in try catch
        return parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../.ini', true);
    }

    /**
     * Loads the guards for the ini configuration file
     * @return array
     */
    public static function guards()
    {
        $ini_settings = require $_SERVER['DOCUMENT_ROOT'] . '/../config/ini.php';
        return isset($ini_settings['protected']) ? $ini_settings['protected'] : [];
    }
}
