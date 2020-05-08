<?php

namespace Express\Abstractions;

use Express\Http\Request;

abstract class Controller
{
    protected $request;

    private $response;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function httpError($reason = false, int $code = 400)
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
        $reason   = (string) $reason ?: 'Unknown Error';

        ob_clean();
        header("$protocol $code $reason", true, $code);
        http_response_code($code);
    }

    /**
     * Allows for JSON object echo within APIs
     *
     * @param $contents
     *
     * @return false|string
     */
    protected function jsonify($contents)
    {
        ob_clean();
        header('Content-Type: application/json;charset=utf-8');
        return json_encode($contents);
    }

    /**
     * Requests an internal function from this
     *
     *
     * @throws \Exception
     */
    public function request($data)
    {
        $method = is_array($data) ? $data['request'] : $data;

        if (!method_exists($this, $method)) {
            throw new \Exception('Method requested does not exist in ' . get_class($this), 400);
        }

        $this->$method($data);
    }
}
