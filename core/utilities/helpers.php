<?php

use Teapot\Teapot;
/**
 * Creates an output string to be echoed.
 * @param any $content
 * @return string
 */
function dump($content)
{
    echo '<pre>';
    var_dump($content);
    echo '</pre>';
}

/**
 * Uses the dump output string to exit execution and display dumped content.
 * @param any $content
 * @return void
 */
function dd($content)
{

    $caller = debug_backtrace(0, 1)[0];

    ob_start();
    dump($content);
    echo '<pre>';
    echo "{$caller['file']} (line: {$caller['line']})";
    echo '</pre>';
    $output = ob_get_clean();
    die($output);
}

/**
 * Prints the output and adds a new line after.
 * @param string $output
 * @return void
 */
function nprint($output)
{
    echo $output . '<br>';
}

/**
 * Loads the configuration option based on a key passed.
 * @param
 * @return any | bool
 */
function config($key)
{
    return \Teapot\Teapot::config($key);
}

/**
 * Loads view contents from the view folder
 *
 * @param string $file
 */
function view($file)
{
    $view_path = __DIR__ . '/../../resources/views/';
    $file      = strpos($file, '.view.php') ? $file : $file . '.view.php';

    if (!file_exists($view_path . $file)) {
        throw new \Exception('Unable to locate view.', 418);
    }

    require $view_path . $file;
};
