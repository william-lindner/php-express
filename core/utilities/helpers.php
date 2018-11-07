<?php
/**
 * Creates an output string to be echoed.
 * @param any $content
 * @return string
 */
function dump($content)
{
    ob_start();
    echo '<pre>';
    var_dump($content);
    echo '</pre>';
    return ob_get_clean();
}

/**
 * Uses the dump output string to exit execution and display dumped content.
 * @param any $content
 * @return void
 */
function dd($content)
{
    die(dump($content));
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

if (!is_callable('config')) {

}
