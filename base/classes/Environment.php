<?php

namespace Express;

class Environment
{

    public static $is = null;

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

        $ip = $_SERVER['REMOTE_ADDR'] ?? 0;

        if ((($ip !== 0 && in_array($ip, ['127.0.0.1', '::1']))
            || config('server.environment', 'prod') === 'local')) {
            static::$is = 'local';
        }

        return (static::$is === 'local');
    }
}
