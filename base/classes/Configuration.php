<?php

namespace Express;

class Configuration
{

    private static $config     = [];
    private static $guards     = [];
    private static $allowables = [];

    public static $resources = [];

    /**
     *
     */
    public static function load(string $file)
    {
        $path = __BASEDIR__ . "/config/{$file}.php";

        if (!isset(self::$config[$file]) && file_exists($path)) {
            return require $path;
        }

        return false;
    }

    /**
     * Sets up the server from the base directory
     *
     * @return void
     */
    public static function setup(string $baseDir)
    {
        self::setConfig($baseDir);

        $timezone = self::$config['server']['timezone'] ?? 'America/Chicago';
        ini_set('date.timezone', $timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * Loads the settings from the ini file
     *
     * @return void
     */
    public static function setConfig(string $baseDir)
    {
        self::$config = parse_ini_file($baseDir . '/.ini', true);
    }

    /**
     * Sets resource locations from the manifest
     *
     * @return void
     */
    public static function setResources(string $baseDir)
    {
        $assets = json_decode(
            @file_get_contents($baseDir . '/mix-manifest.json'),
            true
        );

        static::_parseManifest($assets);
    }

    /**
     * Fetches a value from the configuration.
     *
     * @return any
     */
    public static function get($identifier)
    {

        // TODO rework parsing here to be able to protect config options
        if (in_array($identifier, self::$guards)) {
            return false;
        }

        if (strpos($identifier, '.')) {
            $identifier   = explode('.', $identifier);
            $primary_key  = $identifier[0];
            $tertiary_key = $identifier[1];

            return self::$config[$primary_key][$tertiary_key] ?? false;
        }

        return self::$config[$identifier] ?? false;
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

        return isset(self::$config['Express']['protected']) ? self::$config['Express']['protected'] : [];
    }

    /**
     * Parses the manifest file looking for JS and CSS files
     * @return void
     */
    protected static function _parseManifest(array $manifest)
    {
        static::$resources['scripts'] = [];
        static::$resources['styles']  = [];

        array_walk($manifest, function ($resources, $key) {
            if (is_array($resources)) {
                static::$resources['scripts'][$key] = $resources[0];
                static::$resources['styles'][$key]  = $resources[1];
                return;
            }

            static::$resources['scripts'][$key] = $resources;

        });

    }
}
