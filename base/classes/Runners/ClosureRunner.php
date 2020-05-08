<?php


namespace Express\Runners;


use Express\Injection\InjectionClosure;
use Express\Runners\Abstractions\Runner;
use Express\Runners\Interfaces\Runnable;
use Express\Injection\Interfaces\Injector;

class ClosureRunner extends Runner implements Runnable
{
    public function __construct(\Closure $closure)
    {
        $this->handler = $closure;
    }

    public function inject() : Injector
    {
        return new InjectionClosure($this->handler);
    }
}