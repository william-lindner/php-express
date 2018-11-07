<?php

namespace Teapot;

class Session
{

    protected static $settings;

    const NAME = 'my-session';

    const ONE_HOUR       = 3600;
    const ONE_MINUTE     = 60;
    const DEFAULT_EXPIRY = self::ONE_HOUR * 10;

    const DEFAULT_OPTIONS = [
        'gc_maxlifetime'   => self::DEFAULT_EXPIRY,
        'cookie_lifetime'  => self::DEFAULT_EXPIRY,
        'cookie_httponly'  => 1,
        'use_trans_sid'    => 0,
        'use_only_cookies' => 1,
    ];

    private function __construct()
    {
        throw new \Exception('Unable to instantiate Session.', 418); // no tea!
    }

    public static function start()
    {

        dd(parse_ini_file('./../../.ini'));

        $session_check = session_status() === PHP_SESSION_ACTIVE;
        if ($session_check) {
            return $session_check;
        }

        ini_set('session.gc_maxlifetime', (self::EXPIRY));
        ini_set('session.cookie_lifetime', (self::EXPIRY));
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);

        session_set_cookie_params((self::EXPIRY), '/', '.' . WHERE_AM_I, true);
        session_name(self::NAME);
        session_start();

        return (session_status() === PHP_SESSION_ACTIVE);
    }

    //clears all information from session and signs out user
    public static function destroy($clear_all = true)
    {
        self::start();

        session_unset();
        session_destroy();
        session_abort();

        if ($clear_all) {
            foreach ($_COOKIE as $name => $content) {
                setcookie($name, '', time() - self::ONE_HOUR);
                setcookie($name, '', time() - self::ONE_HOUR, '/');
            }
        }
        setcookie(self::NAME, '', time() - self::ONE_HOUR);

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
}
