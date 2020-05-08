<?php

namespace Express\Http;

class Response
{
    const CONTENT_TYPES = [
        'json' => 'application/json',
    ];

    public $charset;

    public function __construct(?string $contents = null)
    {
        ob_start();

        $this->charset = 'utf-8';
    }

    public function header(string $type)
    {
        $content = self::CONTENT_TYPES[$type];

        header("Content-Type: $content;charset={$this->charset}");

        return $this;
    }

    public function code(int $code = 200)
    {
        http_response_code($code);
        return $this;
    }

    public function send()
    {
        echo ob_get_clean();
    }
}
