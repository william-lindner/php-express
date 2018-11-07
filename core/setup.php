<?php

require __DIR__ . '/utilities/helpers.php';
require __DIR__ . '/utilities/autoload.php';

use Teapot\Teapot;

new Teapot;

dd(config('session'));
