<?php

namespace Express\Http;

class Request
{
    /**
     * The full uri
     *
     * @var string
     */
    private $fullUri;

    /**
     * A parsed uri without query params
     *
     * @var string
     */
    private $uri;

    /**
     * The request method, i.e. POST, GET, PATCH, DELETE
     *
     * @var string
     */
    private $method;

    /**
     * Host information about the request
     *
     * @var string
     */
    private $host;

    /**
     * Server information
     *
     * @var string
     */
    private $server;

    /**
     * Stores the data from the request
     *
     * @var array
     */
    private $data = [];

    /**
     * The route instance for the request
     *
     * @var Route
     */
    private $route;

    /**
     * Extracts specific info points of the super global SERVER for requests.
     *
     * @param array $info
     *
     * @constructor
     */
    public function __construct(array $info = [])
    {
        $this->fullUri = $info['REQUEST_URI'] ?? $_SERVER['REQUEST_URI'];
        $this->uri = $this->parseUri($info['REQUEST_URI'] ?? $_SERVER['REQUEST_URI']);

        $this->route = new Route($this->uri);

        $this->method = $info['REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'];
        //        $this->host = $info['HTTP_HOST'] ?? $_SERVER['HTTP_HOST'];

        if (!empty($post = $_POST)) {
            $post = static::sanitize($post);

            $this->data = $post;
        }

        if (!empty($get = $_GET)) {
            $get = static::sanitize($get);

            $this->data = $get;
        }
    }

    /**
     * Removes any get parameters from the uri string
     *
     * @param string $uri
     *
     * @return string
     */
    protected function parseUri(string $uri) : string
    {
        if (($p = strpos($uri, '?')) === false) {
            return $uri;
        }

        return substr($uri, 0, $p);
    }

    /**
     * Trims strings and removes bad data from the requests.
     * Does this want to be it's own class?
     *
     * @param array $info
     *
     * @return array
     * @api
     */
    public static function sanitize(array $info) : array
    {
        return array_map(
            static function ($attr) {
                if (is_array($attr)) {
                    return static::sanitize($attr);
                }

                if (is_string($attr)) {
                    $attr = strip_tags(trim($attr));
                }

                return $attr;
            },
            $info
        );
    }

    /**
     * Getter to protect private property
     *
     * @return string
     * @api
     */
    public function fullUri() : string
    {
        return $this->fullUri;
    }

    /**
     * Getter to protect private property
     *
     * @return string
     * @api
     */
    public function uri() : string
    {
        return $this->uri;
    }

    /**
     * Getter to protect private property from mutation
     *
     * @return string
     * @api
     */
    public function method() : string
    {
        return $this->method;
    }

    /**
     * Getter to protect the private property from mutation
     *
     * @return Route
     * @api
     */
    public function route() : Route
    {
        return $this->route;
    }

    /**
     * Returns all request data
     *
     * @return array
     * @api
     */
    public function all() : array
    {
        return $this->data;
    }

    /**
     * Returns a specific key or a fallback
     *
     * @param string     $key
     * @param mixed|null $fallback
     *
     * @return mixed|null
     */
    public function input(string $key, $fallback = null)
    {
        return $this->data[$key] ?? $fallback;
    }

    public function only($args)
    {
        // get only these from data
    }

    public function except($args)
    {
        // get all data except
    }

    public function has($args)
    {
        // does the key or keys exist in data
    }

    public function flash()
    {
        // session storage
    }

    /**
     * Exposes the info for debugging.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'method' => $this->method,
            'uri'    => $this->fullUri,
            'route'  => $this->route
        ];
    }
}
