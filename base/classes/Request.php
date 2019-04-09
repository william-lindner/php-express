<?php

namespace Express;

class Request implements \IteratorAggregate
{

    private $data = [];

    /**
     * Extracts specific data points of the super global SERVER for requests.
     *
     * @return Request
     */
    public function __construct(array $data = [])
    {
        $this->data['uri']    = $data['REQUEST_URI'] ?? $_SERVER['REQUEST_URI'];
        $this->data['method'] = $data['REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'];
        $this->data['host']   = $data['HTTP_HOST'] ?? $_SERVER['HTTP_HOST'];
        $this->data['server'] = $data['SERVER_NAME'] ?? $_SERVER['SERVER_NAME'];

        // todo - rewrite this with a single pattern
        $uriData = preg_split('/\?|&/', $this->data['uri']);
        $uri     = array_shift($uriData);
        $uri .= implode('', preg_replace('/(^.+)(=)(.+)/', '{$1}', $uriData));
        $this->data['uri'] = $uri;

        // $uri = preg_replace('/(\?)(\w+)(=)(.+)/', '{$2}', $this->data['uri']);

        if (!empty($_POST)) {
            $_POST = sanitize($_POST);
        }

        if (!empty($_GET)) {
            $_GET = sanitize($_GET);
        }

        $this->data['post'] = $_POST;
        $this->data['get']  = $_GET;
    }

    /**
     * Allows for access to data variables through instance.
     *
     * @return string
     */
    public function __invoke(string $key)
    {
        return $this->data[strtolower($key)];
    }

    /**
     * Exposes the data for debugging.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->data;
    }

    /**
     * Instance getter returning data attributes
     *
     * @return any
     */
    public function __get(string $key)
    {
        return $this->data[strtolower($key)] ?? null;
    }

    /**
     * Required as implementation of IteratorAggregate. Allows iteration of instance.
     *
     * @return array
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
