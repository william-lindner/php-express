<?php

namespace Express\Interfaces;

interface RouteHandler
{
    public function buildParameters(\Reflector $reflector);

    public function load();
}
