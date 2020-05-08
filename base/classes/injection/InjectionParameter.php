<?php

namespace Express\Injection;

use ReflectionParameter;

class InjectionParameter
{

    /**
     * The reflected parameter to be injected
     *
     * @var ReflectionParameter
     */
    private $parameter;

    /**
     * Is the parameter optional
     *
     * @var bool
     */
    private $optional;

    public function __construct(ReflectionParameter $parameter)
    {
        $this->parameter = $parameter;

        $this->optional = $parameter->isOptional();

        dump($this->parameter);

        dump($this->parameter->getType());

        dump($this->parameter->getName());
    }

    public function getName()
    {

    }

    public function build()
    {


    }
}
