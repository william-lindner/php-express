<?php

class RouteController implements RouteHandler
{
    /**
     *
     *
     * @var
     */
    protected $object;

    public function __construct(\stdClass $object, $data = [])
    {
        $this->object = $object;
        $this->buildParameters(new \ReflectionClass($object), $data);
    }
}
