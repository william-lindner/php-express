<?php

namespace Express;

use RuntimeException;

/*
|--------------------------------------------------------------------------
| Express Session
|--------------------------------------------------------------------------
|
*/

class Session
{
    /**
     * An extensible default expiration time frame. Configurable using root .ini too.
     *
     * @const
     */
    protected const DEFAULT_EXPIRY = 36000;
    /**
     * String output provided as message when session_start runtime exception is thrown
     *
     * @const
     */
    protected const SESSION_FAILED_TO_START = 'Unable to start the session';
    /**
     * String output provided as message when session cookie cannot be written
     *
     * @const
     */
    protected const COOKIE_FAILED_WRITE = 'Could not write to cookie, headers already set';
    /**
     * Stores the session data in a mutable state, so that the super global
     * is only ever accessed as read only.
     *
     * @var array
     */
    private static $data = [];

    /**
     * Session constructor.
     *
     * @throws RuntimeException
     */
    public function __construct()
    {
        if (self::$data !== [] || session_status() === PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Session is already active', 400);
        }

        $this->configure()->start(['read_and_close' => true]);

        self::$data = $_SESSION;

        $this->checkExpiration();

        session_abort();
    }

    /**
     * Starts the session, throwing runtime exception when unable to initiate
     *
     * @param array $options
     *
     * @return Session
     */
    protected function start($options = []) : Session
    {
        if (!session_start($options)) {
            throw new RuntimeException(static::SESSION_FAILED_TO_START, 500);
        }

        return $this;
    }

    /**
     * Configures the ini setup according to your local ini config. Ideally, these
     * would be setup on the server.
     *
     * @return Session
     */
    protected function configure() : Session
    {
        // static configurations
        ini_set('session.use_trans_sid', 0);

        // cookie configurations
        session_name(config('session.name', 'express'));

        session_set_cookie_params(
            [
                'lifetime' => config('session.max_lifetime', self::DEFAULT_EXPIRY),
                'secure'   => config('session.ssl_require', false),
                'httponly' => config('session.http_only', true),
            ]
        );

        return $this;
    }

    /**
     * Regenerates the session identifier
     *
     * @return Session
     */
    protected function checkExpiration() : Session
    {
        $time = time();

        if (!isset(self::$data['_session_regen_at'])) {
            self::$data['_session_regen_at'] = $time;

            return $this;
        }

        if (self::$data['_session_regen_at'] + 1800 < $time) {
            session_regenerate_id(true);

            self::$data['_session_regen_at'] = $time;
        }

        return $this;
    }

    /**
     * Store a key value pair in the session
     *
     * @param string $key
     * @param mixed  $value
     *
     * @api
     */
    public static function set(string $key, $value = null) : void
    {
        self::$data[$key] = $value;
    }

    /**
     * Gets the key value from the session
     *
     * @param string $key
     *
     * @return mixed|null
     * @api
     */
    public static function get(string $key)
    {
        return self::$data[$key] ?? null;
    }

    /**
     * Destroys the session and unsets any information related to it
     *
     * @return bool
     * @api
     */
    public static function destroy() : bool
    {
        if (!setcookie(config('session.name', 'express'), '', time() - 3600)) {
            throw new RuntimeException(static::COOKIE_FAILED_WRITE, 500);
        }

        // when the session isn't already started, start it
        if (!(session_status() === PHP_SESSION_ACTIVE || session_start())) {
            throw new RuntimeException(static::SESSION_FAILED_TO_START);
        }

        self::$data = $_SESSION = [];
        session_write_close();

        return session_status() !== PHP_SESSION_ACTIVE;
    }

    /**
     * Dumping the session provides the data as output.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return self::$data;
    }

    /**
     * Upon destruct write and close the session, recording local data.
     *
     */
    public function __destruct()
    {
        $this->writeAndClose();
    }

    /**
     *
     *
     * @return bool|void
     */
    protected function writeAndClose()
    {
        if (!(session_status() === PHP_SESSION_ACTIVE || session_start())) {
            return false;
        }

        $_SESSION = self::$data;

        return session_write_close();
    }
}
