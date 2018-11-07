<?php

require './../core/setup.php';

use Teapot\Session;

nprint('Teapot placed on burner...');

$session = Session::start();

nprint('Teapot boiled.');
