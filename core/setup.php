<?php

define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
define('FILE', $_SERVER['PHP_SELF']);
define('FILE_PATH', $_SERVER['SCRIPT_FILENAME']);
define('METHOD', $_SERVER['REQUEST_METHOD']);
define('AGENT', $_SERVER['HTTP_USER_AGENT']);
define('LANG', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
define('WHERE_AM_I', $_SERVER['SERVER_NAME']);
define('HTTPS_PATH', 'https://' . WHERE_AM_I);

define('ASSET_PATH', ROOT_PATH . '../assets/');
define('SETTINGS_PATH', ROOT_PATH . '../settings/');
define('DISPLAY_ERRORS', 1);

// load the class framework for the site
spl_autoload_register(function ($class) {
    // This forces a strict formatting of capitol on first character
    $class = ucfirst(strtolower($class));
    if (class_exists($class)) {
        return;
    }

    $class_paths = [
        'classes'     => __DIR__ . '/classes/',
        'controllers' => ROOT_PATH . '/controllers/',
        'models'      => ROOT_PATH . '/models/',
        'views'       => ROOT_PATH . '/views/',
    ];

    $file_name = $class . '.php';
    foreach ($class_paths as $path) {
        if (file_exists($path . $file_name)) {
            include $path . $file_name;
            break;
        }
    }
});

// Force configuration of timezones to CT on the server
ini_set('date.timezone', 'America/Chicago');
date_default_timezone_set('America/Chicago');

require ROOT_PATH . 'views/head.php';
