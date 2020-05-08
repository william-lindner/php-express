<?php

namespace Express\Abstractions;

abstract class Controller
{
    protected function httpError(?string $reason = null, int $code = 400)
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
        $reason = (string) $reason ?: 'Unknown Error';

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
}
