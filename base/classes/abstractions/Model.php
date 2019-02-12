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

    /**
     * A wrapper function that allows you to request a method via a bundled
     * set of data in JS, one of which will be labeled 'request'
     */
    public function request($request = null, $data = [])
    {
        // There are occasions when a request is passed as part of the array.
        // This mostly occurs when AJAX is involved. The following code handles this use case.
        if (is_array($request)) {
            $data    = $request;
            $request = $request['request'] ?? null;
            unset($data['request']);
        }

        if (!$request || !is_string($request)) {
            $this->returnError('No request passed to the model.', 400);
        }
        if (!method_exists($this, $request)) {
            $this->returnError('Request method does not exist.', 400);
        }

        $results = $this->{$request}($data);
        if ($results !== null) {
            $this->echoResults($results);
        }

        $this->returnError('No results returned from Model', 400);
    }

    protected function returnError($reason = false, $code = 418)
    {

        $protocol = $_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.1';
        $reason   = $reason ?: 'No Tea';
        $code     = (int) $code;

        ob_clean();
        header("$protocol $code $reason", true, $code);
        http_response_code($code);
    }

    protected function echoResults($results = [])
    {
        ob_clean();
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($results);
        exit;
    }

}
