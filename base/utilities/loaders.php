<?php

use Express\Configuration;
use Express\View;

if (!function_exists('view')) {
    /**
     * Loads view contents from the view folder
     *
     * @param string $file
     * @param array  $data
     *
     * @return false|string
     * @throws Exception
     */
    function view(string $file, array $data = [])
    {
        return (new View($data))->render($file);
    }
}

/**
 * Designed to load the Mentoring resources (i.e. scripts and stylesheets)
 *
 * @param string $type
 * @param string $which
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
