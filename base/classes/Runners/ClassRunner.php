<?php


namespace Express\Runners;

use RuntimeException;
use ReflectionException;
use Express\Injection\InjectionMethod;
use Express\Exception\MissingParameter;
use Express\Runners\Abstractions\Runner;
use Express\Runners\Interfaces\Runnable;
use Express\Injection\Interfaces\Injector;

class ClassRunner extends Runner implements Runnable
{
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     *
     *
     * @return Injector
     * @throws MissingParameter
     * @throws ReflectionException
     */
    public function inject() : Injector
    {
        [$className, $method] = explode('@', $this->handler);

        if (!$method) {
            throw new RuntimeException('No method provided for class', 500);
        }

        $className = '\\App\\Controllers\\' . $className;

        return new InjectionMethod(new $className(), $method);
    }
}