<?php

namespace Express\Abstractions;

use Express\PDO;

abstract class Model
{
    protected $db;

    private $data;

    public function __construct()
    {
        $this->db = new PDO();
    }

    protected function httpError($reason = false, $code = 418)
    {

        $protocol = $_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.1';
        $reason   = $reason ?: 'No Tea';
        $code     = (int) $code;

        ob_clean();
        header("$protocol $code $reason", true, $code);
        http_response_code($code);
    }

    /**
     * Allows for JSON object echo within APIs
     *
     */
    protected function jsonify($contents)
    {
        ob_clean();
        header('Content-Type: application/json;charset=utf-8');
        exit(json_encode($contents));
    }

}
