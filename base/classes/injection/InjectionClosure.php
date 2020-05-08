<?php

namespace Express\Injection;

use Closure;
use ReflectionException;
use Express\Exception\MissingParameter;
use Express\Injection\Interfaces\Injector;
use Express\Injection\Abstractions\ParameterInjector;

class InjectionClosure extends ParameterInjector implements Injector
{
    /**
     * The closure to be ran
     *
     * @var
     */
    protected $closure;

    /**
     * InjectionClosure constructor.
     *
     * @param Closure $closure
     * @param array   $data
     *
     * @throws MissingParameter
     * @throws ReflectionException
     */
    public function __construct(Closure $closure, $data = [])
    {
        $this->closure = $closure;

        $this->injectParameters(new \ReflectionFunction($closure), $data);
    }

    /**
     * Executes the injector
     */
    public function exec()
    {
        return ($this->closure)(...$this->parameters);
    }
}
