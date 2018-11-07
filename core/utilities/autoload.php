<?php

spl_autoload_register(function ($class) {
    if (strpos($class, 'Teapot') === false) {
        return;
    }
    $class = array_pop(explode('\\', $class));
    $path  = __DIR__ . "/../classes/{$class}.php";
    if (file_exists($path)) {
        require_once $path;
    }
});
