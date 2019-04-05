<?php

use App\Middleware\Authenticate;
use App\Middleware\Authorize;
use Express\Express;
use Express\Request;
use Express\Visitor;

define('__BASEDIR__', __DIR__ . '/..');
define('__VIEWDIR__', __DIR__ . '/../resources/views');

require __BASEDIR__ . '/vendor/autoload.php';

$express = new Express(new Request($_SERVER), new Visitor());

$express->beforeRoute(Authorize::class);
$express->beforeEnd(function ($request, $visitor) {
    Authenticate::destroy();
});

$express->run();
