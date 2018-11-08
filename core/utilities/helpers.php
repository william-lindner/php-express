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
    ob_start();
    dump($content);
    debug_print_backtrace();
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
