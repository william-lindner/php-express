<?php

namespace Express;

/*
|--------------------------------------------------------------------------
| Express Environment
|--------------------------------------------------------------------------
|
*/


class Environment
{

    public static $is = null;

    public static function isDev()
    {
        if (static::$is === 'dev' || static::$is === 'local') {
            return true;
        }

        return static::$is = Configuration::get('server.environment', 'prod') === 'dev';
    }

    /**
     * Determines if the environment is local based on IP
     *
     * @return bool
     */
    public static function isLocal()
    {
        if (static::$is === 'local') {
            return true;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0';

        if ((($ip !== '0' && in_array($ip, ['127.0.0.1', '::1']))
             || Configuration::get('server.environment', 'prod') === 'local')) {
            static::$is = 'local';
        }

        return (static::$is === 'local');
    }
}
