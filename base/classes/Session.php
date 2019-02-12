<?php

namespace Express;

class Session implements \IteratorAggregate
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

    private $state;
    private $sessionData;

    protected $config;
    protected static $instance;

    private function __construct()
    {
        ini_set('session.gc_maxlifetime', (self::DEFAULT_EXPIRY));
        ini_set('session.cookie_lifetime', (self::DEFAULT_EXPIRY));
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);

        session_set_cookie_params((self::DEFAULT_EXPIRY), '/', '.' . 'localhost', true);
        session_name('my-session');

    }

    /**
     * Builds the session instance and starts the session
     *
     * @return Session
     */
    public static function start()
    {

        static::$instance = static::$instance ?? new static();

        if (session_status() === PHP_SESSION_ACTIVE) {
            return static::$instance;
        }

        session_start();
        return static::$instance;
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

        setcookie('my-session', '', time() - self::ONE_HOUR);

        return !(session_status() === PHP_SESSION_ACTIVE);
    }

    // This regenerates the session id every half hour.
    // Which helps prevent someone from acquiring the information from our cookie
    // and using it maliciously.
    public static function regenerate()
    {
        if (!isset($_SESSION['_id_expires_on'])) {
            $_SESSION['_id_expires_on'] = time();
        }

        if ($_SESSION['_id_expires_on'] + self::ONE_HOUR / 2 < time()) {
            session_regenerate_id(true);
            $_SESSION['_id_expires_on'] = time();
        }
    }

    /**
     *
     */
    public function getIterator()
    {
        return new \ArrayIterator($_SESSION);
    }
}
