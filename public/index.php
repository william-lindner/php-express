<?php

use Express\Express;
use Express\Handlers\Request;

define('__BASEDIR__', __DIR__ . '/..');
define('__VIEWDIR__', __DIR__ . '/../resources/views');

require __BASEDIR__ . '/vendor/autoload.php';

$express = new Express(new Request($_SERVER));
global $express;
$express->run();
