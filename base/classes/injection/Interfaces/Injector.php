<?php

namespace Express\Injection\Interfaces;

interface Injector
{
    public function injectParameters(\Reflector $reflector);

    public function exec();
}