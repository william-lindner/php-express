<?php


namespace Express\Runners\Interfaces;


use Express\Injection\Interfaces\Injector;

interface Runnable
{
    public function inject() : Injector;
}