<?php

namespace Express\Injection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Express\Exception\MissingParameter;
use Express\Injection\Interfaces\Injector;
use Express\Injection\Abstractions\ParameterInjector;

class InjectionMethod extends ParameterInjector implements Injector
{
    /**
     *
     *
     * @var
     */
    private $object;

    private $method;

    /**
     * InjectionMethod constructor.
     *
     * @param       $class
     * @param       $method
     * @param array $data
     *
     * @throws MissingParameter
     * @throws ReflectionException
     */
    public function __construct($class, $method, $data = [])
    {
        if (is_string($class)) {
            $class = new ReflectionClass($class);
        }

        $this->object = $class;
        $this->method = $method;
        $this->injectParameters(new ReflectionMethod($class, $method), $data);
    }

    public function exec()
    {
        return $this->object->{$this->method}(...$this->parameters);
    }
}
