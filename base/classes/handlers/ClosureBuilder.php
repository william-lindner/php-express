<?php

namespace Express\Handlers;

use Express\Interfaces\RouteHandler;
use Express\Exception\MissingParameter;
use Express\Request;

class ClosureBuilder implements RouteHandler
{
    public $parameters = [];

    protected $closure;

    public function __construct(\Closure $closure, $data = [])
    {
        $this->closure = $closure;
        $this->buildParameters(new \ReflectionFunction($closure), $data);
    }

    public function buildParameters(\Reflector $reflector, array $data = [])
    {
        $data = array_reverse($data);

        $activeParam = null;
        foreach ($params = $reflector->getParameters() as $param) {
            $activeParam = $activeParam ?? array_pop($data);
            if ($classReflector = $param->getClass()) {
                $className = $classReflector->getName();

                if ($activeParam instanceof $className) {
                    $this->parameters[] = $activeParam;
                    $activeParam        = null;
                    continue;
                }

                $this->parameters[] = new $className();
                continue;
            }

            if (!$param->isOptional() and !isset($activeParam)) {
                throw new MissingParameter();
            }

            $this->parameters[] = $activeParam;
            $activeParam        = null;
        }
    }

    public function load()
    {
        $func = $this->closure;
        $func(...$this->parameters);
    }
}
