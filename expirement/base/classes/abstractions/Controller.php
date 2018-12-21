<?php

namespace Teapot\Abstractions;

abstract class Controller
{
    protected $request;
    protected $action;

    public function __construct($action, $request)
    {
        $this->action  = $action;
        $this->request = $request;
    }

    public function executeAction()
    {
        return $this->{$this->action}();
    }

    public function returnView()
    {
        $view = 'views/' . strtolower(get_class($this)) . '/' . $this->action . '.php';
        var_dump($view);
        require $view;
    }
}
