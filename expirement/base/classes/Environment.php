<?php

namespace Teapot;

class Environment
{
    /**
     * Determines if the environment is local based on IP
     *
     * @return bool
     */
    public static function isLocal()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 0;

        return (($ip !== 0 && in_array($ip, ['127.0.0.1', '::1']))
            || config('server.environment') === 'local');
    }
}
