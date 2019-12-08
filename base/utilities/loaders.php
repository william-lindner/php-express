<?php

use Express\Configuration;
use Express\View;

/**
 * Loads view contents from the view folder
 *
 * @param string $file
 */
function view(string $file, array $data = [])
{
    return (new View($data))->render($file);
};

/**
 * Loads query contents from the query folder
 *
 * @return string
 */
function query(string $file)
{
    return require __BASEDIR__ . "/app/queries/{$file}.php";
}

/**
 * Designed to load the Mentoring resources (i.e. scripts and stylesheets)
 *
 *
 */
function resource(string $type, string $which)
{
    $resource = Configuration::$resources[$type][$which] ?? [];

    $templates = [
        'scripts' => [
            '<script src="',
            '" type="text/javascript" defer></script>',
        ],
        'styles'  => [
            '<link rel="stylesheet" type="text/css" href="',
            '">',
        ],
    ];

    $resource = '/includes/' . $resource;
    echo implode($resource, $templates[$type]);
}
