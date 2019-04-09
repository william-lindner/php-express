<?php

namespace Express;

class Session
{

    const ONE_HOUR        = 3600;
    const ONE_MINUTE      = 60;
    const DEFAULT_EXPIRY  = self::ONE_HOUR * 10;
    const DEFAULT_OPTIONS = [
        'gc_maxlifetime'   => self::DEFAULT_EXPIRY,
        'cookie_lifetime'  => self::DEFAULT_EXPIRY,
        'cookie_httponly'  => 1,
        'use_trans_sid'    => 0,
        'use_only_cookies' => 1,
    ];

    protected $session;

    private static function configure()
    {

        ini_set(
            'session.gc_maxlifetime',
            (config('session.max_lifetime') ?? self::DEFAULT_EXPIRY)
        );
        ini_set(
            'session.cookie_lifetime',
            (config('session.max_lifetime') ?? self::DEFAULT_EXPIRY)
        );
        ini_set(
            'session.cookie_httponly', 1
        );
        ini_set('session.use_trans_sid', 0);
        ini_set(
            'session.use_only_cookies',
            config('session.use_only_cookies') ?? 1
        );
        ini_set('session.cookie_secure', 1);

        // session_set_cookie_params((self::DEFAULT_EXPIRY), '/', '.' . 'localhost', true);
        session_name(config('session.name') ?? 'my-session');
    }

    /**
     * Builds the session configuration from constructor and starts the session
     *
     * @return bool
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        self::configure();

        return session_start();
    }

    /**
     * Clears session information and stops the session.
     *
     * @return bool
     */
    public static function stop()
    {
        static::start();

        session_unset();
        session_destroy();
        session_abort();

        setcookie(config('session.name'), '', time() - self::ONE_HOUR);

        return !(session_status() === PHP_SESSION_ACTIVE);
    }

    /**
     * Regenerates the session identifier
     *
     * @return void
     */
    public static function regenerate()
    {
        if (!isset($_SESSION['_id_expires_at'])) {
            $_SESSION['_id_expires_at'] = time();
            return;
        }

        if ($_SESSION['_id_expires_at'] + self::ONE_HOUR / 2 < time()) {
            session_regenerate_id(true);
            $_SESSION['_id_expires_at'] = time();
        }
    }
}
