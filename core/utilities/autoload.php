<?php

spl_autoload_register(function ($class) {
    if (strpos($class, 'Teapot') === false) {
        return;
    }
    $request = explode('\\', $class);
    $class   = array_pop($request);

    $path = __DIR__ . "/../classes/{$class}.php";
    if (file_exists($path)) {
        require_once $path;
    }
});
