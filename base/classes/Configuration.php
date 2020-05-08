<?php

namespace Express;

use RuntimeException;
use Express\Handlers\Exception;

class Configuration
{
    public static $resources = []; // @todo refactor Resources class

    /**
     * Stores the configuration for the Express server
     *
     * @var array
     */
    private static $ini = [];

    /**
     * Sets the local instance of the configuration singleton
     *
     * @var Configuration
     */
    private static $config = null;

    /**
     * Configuration constructor.
     *
     * @constructor
     */
    protected function __construct()
    {
        if (!defined('__BASEDIR__') && isset($_SERVER)) {
            define('__BASEDIR__', $_SERVER['DOCUMENT_ROOT'] . '/..');
        }

        self::$ini = $this->parseIni();
    }

    /**
     * Parses the local configuration file into an associative array
     *
     */
    protected function parseIni()
    {
        $path = __BASEDIR__ . '/.ini';

        if (!file_exists($path)) {
            copy(__BASEDIR__ . '/.ini.example', $path);
        }

        if (!$ini = parse_ini_file($path, true)) {
            throw new RuntimeException('Unable to load configuration settings', 500);
        }

        return $ini;
    }

    /**
     * @param string $file
     *
     * @return bool|mixed
     */
    public static function load(string $file)
    {
        $path = __BASEDIR__ . "/config/{$file}.php";

        if (file_exists($path)) {
            return require $path;
        }

        return false;
    }

    /**
     * Sets up the server from the base directory
     *
     * @return void
     */
    public static function setup() : void
    {
        if (self::$config !== null) {
            return;
        }

        self::$config = new static();

        self::$config->setReporting();
        self::$config->setTimezone();
    }

    /**
     * Sets error and exception handling / reporting
     *
     */
    protected function setReporting() : void
    {
        Exception::register();

        ini_set('display_errors', static::get('server.display_errors', 1));
        error_reporting(static::get('server.error_reporting', 1));
    }

    /**
     * Fetches a value from the configuration.
     *
     * @param string $identifier
     * @param mixed  $fallback
     *
     * @return mixed
     */
    public static function get(string $identifier, $fallback = null)
    {
        if (strpos($identifier, '.') !== false) {
            [$oKey, $vKey] = explode('.', $identifier);

            return self::$ini[$oKey][$vKey] ?? $fallback;
        }

        return self::$ini[$identifier] ?? $fallback;
    }

    /**
     * Sets the server timezone, defaulting to false
     *
     */
    protected function setTimezone() : void
    {
        $timezone = self::$ini['server']['timezone'] ?? 'America/Chicago';

        ini_set('date.timezone', $timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * Sets resource locations from the manifest
     *
     * @return void
     * @todo refactor
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
     * Parses the manifest file looking for JS and CSS files
     *
     * @return void
     * @todo refactor
     */
    protected static function _parseManifest(array $manifest) : void
    {
        static::$resources['scripts'] = [];
        static::$resources['styles'] = [];

        array_walk(
            $manifest, static function ($resources, $key) {
            if (is_array($resources)) {
                static::$resources['scripts'][$key] = $resources[0];
                static::$resources['styles'][$key] = $resources[1];
                return;
            }

            static::$resources['scripts'][$key] = $resources;
        }
        );
    }
}
