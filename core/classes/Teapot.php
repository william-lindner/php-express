<?php

namespace Teapot;

use Teapot\Session;

final class Teapot
{
    protected $settings;

    public function __construct()
    {

        // Note: not certain about the include path takeover
        set_include_path($_SERVER['DOCUMENT_ROOT'] . '/../app');
        ini_set('include_path', $_SERVER['DOCUMENT_ROOT'] . '/../app');
        Exception::register();
        Session::start();

        require 'routes.php';

    }

}
