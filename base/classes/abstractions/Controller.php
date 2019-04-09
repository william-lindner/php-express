<?php

namespace Express\Abstractions;

abstract class Controller
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
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

    /**
     * Requests an internal function from this
     */
    public function request($data)
    {
        $method = is_array($data) ? $data['request'] : $data;

        if (!method_exists($this, $method)) {
            throw new \Exception('Method requested does not exist in ' . get_class($this), 400);
        }

        $this->$method();
    }

}
