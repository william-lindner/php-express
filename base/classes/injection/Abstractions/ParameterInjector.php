<?php

namespace Express\Injection\Abstractions;

use Reflector;
use Express\Exception\MissingParameter;

abstract class ParameterInjector
{

    protected $parameters = [];


    /**
     * Builds the local parameters array with the data provided
     *
     * @param Reflector $reflector
     * @param array     $data
     *
     * @throws MissingParameter
     */
    public function injectParameters(Reflector $reflector, $data = []) : void
    {
        $data = array_reverse($data);

        $activeParam = null;
        foreach ($params = $reflector->getParameters() as $param) {

            $activeParam = $activeParam ?? array_pop($data);

            if ($classReflector = $param->getClass()) {
                $className = $classReflector->getName();

                if ($activeParam instanceof $className) {
                    $this->parameters[] = $activeParam;
                    $activeParam = null;
                    continue;
                }

                $this->parameters[] = new $className();
                continue;
            }


            if (!isset($activeParam) && !$param->isOptional()) {
                throw new MissingParameter('Required parameter not able to be identified', 400);
            }

            $this->parameters[] = $activeParam;
            $activeParam = null;
        }
    }
}