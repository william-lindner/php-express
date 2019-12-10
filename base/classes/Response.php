<?php

namespace Express;

class Response
{
    private static $instance;

    public function __construct($data = [])
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        ob_start();
        return self::$instance = $this;
    }

    public function code(int $code = 500)
    {
        \http_response_code($code);
        return $this;
    }
}
