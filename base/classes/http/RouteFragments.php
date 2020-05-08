<?php

namespace Express\Http;

use ArrayIterator;

class RouteFragments implements \IteratorAggregate
{
    /**
     * Holds the pieces of the route for fragment matching
     *
     * @var array
     */
    private $fragments;

    /**
     * RouteFragments constructor.
     *
     * @param Route $route
     *
     * @constructor
     */
    public function __construct(Route $route)
    {
        $pieces = explode('/', ltrim($route->uri(), '/'));

        if ($pieces[count($pieces) - 1] === '') {
            unset($pieces[count($pieces) - 1]);
        }

        $this->fragments = $pieces;
    }

    /**
     * Contract iterator object
     *
     * @return ArrayIterator
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->fragments);
    }
}