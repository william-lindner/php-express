<?php

namespace Express\Http;

class Response
{
    const CONTENT_TYPES = [
        'json' => '',
    ];

    public function __construct(?string $contents = null)
    {
        ob_start();
    }

    public function header(string $type)
    {
        header('Content-Type: application/json;charset=utf-8');

        return $this;
    }

    public function get()
    {
    }
}
