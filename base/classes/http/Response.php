<?php

namespace Express\Http;

class Response
{

    public const CONTENT_HTML = 'html';

    protected const DEFAULT_TYPE = 'html';

    private const CONTENT_TYPES = [
        'json'      => 'application/json',
        'text'      => 'text/html',
        'html'      => 'text/html',
        'form-data' => 'multipart/form-data',
        'xml'       => 'application/xml'
    ];

    public $charset;

    public $contentType;

    public $output = '';

    private $buffering = false;

    public function __construct(?string $contents = null)
    {
        $this->contentType = self::CONTENT_TYPES[static::DEFAULT_TYPE];
        $this->charset = 'utf-8';

        if ($contents === null) {
            $this->output = $contents;


        } else {
            $this->buffering = ob_start();
        }

    }

    public function code(int $code = 200)
    {
        http_response_code($code);
        return $this;
    }

    public function json(array $data = [])
    {
        $this->charset = 'utf-8';
        $this->header('json');

        $this->output = json_encode($data);
    }

    public function header(string $type)
    {
        $content = self::CONTENT_TYPES[$type];

        header("Content-Type: $content;charset={$this->charset}");

        return $this;
    }

    public function error(string $reason = '', int $code = 400)
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
        $reason = $reason ?: 'Unknown Error';

        header("$protocol $code $reason", true, $code);
        http_response_code($code);
    }


    /**
     *
     */
    public function send() : void
    {
        if ($this->buffering) {
            $this->output = ob_get_clean();
        }

        echo $this->output;
    }
}
