<?php

namespace Express\Http;

use Express\Container;
use Express\Http\Response\Headers;

class Response
{
    private $contents = '';

    private $buffering = false;

    public function __construct(?string $contents = null, $type = 'html')
    {
        if (!$header = Container::retrieve('headers')) {
            $header = Container::store('headers', new Headers());
        }

        $header->type($type);

        if ($contents !== null) {
            $this->contents = $contents;
        } else {
            $this->buffer();
        }
    }

    /**
     * Turn on the output buffer
     *
     * @return bool
     */
    private function buffer() : bool
    {
        if (!$this->buffering) {
            $this->buffering = ob_start();
        }

        return $this->buffering;
    }

    /**
     * Restart the output buffer, if running.
     *
     * @return bool
     * @api
     */
    public function restart() : bool
    {
        if ($this->buffering) {
            ob_end_clean();
        }

        return $this->buffer();
    }

    /**
     * Compiles a json encoded response
     *
     * @param array $data
     *
     * @return string
     */
    public function json(array $data = []) : string
    {
        $this->header()->type('json');

        $contents = json_encode($data);

        if ($this->buffering) {
            echo $contents;
        } else {
            $this->contents = $contents;
        }

        return $this->content();
    }

    /**
     *
     *
     * @return Headers
     */
    public function header() : Headers
    {
        return Container::retrieve('headers');
    }

    public function content() : string
    {
        if ($this->buffering) {
            $this->contents = ob_get_clean();
        }

        if (!is_string($this->contents)) {
            // error state
        }

        return $this->contents;
    }

    /**
     * Set the HTTP response code, optionally with customized reason
     *
     * @param int    $code
     * @param string $reason
     *
     * @return Response
     */
    public function status(int $code, string $reason = '') : Response
    {
        if ($reason === '') {
            http_response_code($code);
            return $this;
        }

        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';

        header("$protocol $code $reason", true, $code);

        return $this;
    }
}
