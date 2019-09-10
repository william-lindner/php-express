<?php

namespace Express;

final class Session
{
    public $data = [];

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

    protected static $instance = null;

    public function __construct()
    {
        if (self::$instance) {
            return self::$instance;
        }

        $this->start();
        $this->regenerate();
        return self::$instance = $this;
    }

    private function start()
    {
        $this->configure();
        session_start([
            'read_and_close' => true,
        ]);
        $this->data = $_SESSION;
    }

    private function configure()
    {
        ini_set(
            'session.gc_maxlifetime',
            config('session.max_lifetime', self::DEFAULT_EXPIRY)
        );
        ini_set(
            'session.cookie_lifetime',
            config('session.max_lifetime', self::DEFAULT_EXPIRY)
        );
        ini_set(
            'session.cookie_httponly',
            1
        );
        ini_set('session.use_trans_sid', 0);
        ini_set(
            'session.use_only_cookies',
            config('session.use_only_cookies', 1)
        );
        ini_set('session.cookie_secure', 1);

        session_name(config('session.name', 'express'));
    }

    public function close()
    {
        session_start();
        $_SESSION = $this->data;
        session_write_close();
    }

    /**
     * Clears session information and stops the session.
     *
     * @return bool
     */
    public function stop()
    {
        if (!(session_status() === PHP_SESSION_ACTIVE)) {
            return true;
        }

        session_unset();
        session_destroy();
        session_abort();

        return !(session_status() === PHP_SESSION_ACTIVE);
    }

    public function destroy()
    {
        $this->close();
        $this->stop();
        setcookie(config('session.name', 'express'), '', time() - self::ONE_HOUR);
    }

    /**
     * Regenerates the session identifier
     *
     * @return void
     */
    public function regenerate()
    {
        $time = time();

        if (!isset($this->data['_id_expires_at'])) {
            $this->data['_id_expires_at'] = $time;
            return;
        }

        if ($this->data['_id_expires_at'] + self::ONE_HOUR / 2 < $time) {
            session_regenerate_id(true);
            $this->data['_id_expires_at'] = $time;
        }
    }

    public function __debugInfo()
    {
        return $this->data;
    }

    public function __destruct()
    {
        $this->close();
    }
}
