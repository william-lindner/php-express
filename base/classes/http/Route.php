<?php


namespace Express\Http;

class Route
{

    /**
     * Compiled string stored from the constructor
     *
     * @var string
     */
    private $uri;

    /**
     * Fragments of the uri
     *
     * @var array
     */
    private $fragments;

    /**
     * Route constructor.
     *
     * @param string $uri
     */
    public function __construct(string $uri)
    {
        $uri = $this->stripQueryParams($uri);
        $uri = $this->formatUri($uri);

        $this->uri = $uri;

        $this->fragments = new RouteFragments($this);
    }

    /**
     * Identifies and removes query parameters from the uri
     *
     * @param string $uri
     *
     * @return string
     */
    private function stripQueryParams(string $uri) : string
    {
        if (($p = strpos($uri, '?')) === false) {
            return $uri;
        }

        if (!$p = substr($uri, 0, $p)) {
            throw new \RuntimeException('Unable to read route uri', 500);
        }

        return $p;
    }

    /**
     * Maintains a consistent formatting for uris provided to the router
     *
     * @param string $uri
     *
     * @return string
     */
    protected function formatUri(string $uri) : string
    {
        if (strpos('/', $uri[0]) !== 0) {
            $uri = '/' . $uri;
        }

        return strlen($uri) > 1 ? rtrim($uri, '/ \t\n\r\0\x0B') : $uri;
    }

    public function uri() : string
    {
        return $this->uri;
    }

    public function fragments() : array
    {
        return $this->fragments;
    }
}