<?php

if (!function_exists('dump')) {
    /**
     * Creates an output string to be echoed.
     *
     * @param mixed $content
     */
    function dump($content)
    {
        echo '<pre>';
        var_dump($content);
        echo '</pre>';
    }
}

if (!function_exists('dd')) {
    /**
     * Uses the dump output string to exit execution and display dumped content.
     *
     * @param mixed $content
     *
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
}

if (!function_exists('nprint')) {
    /**
     * Prints the output and adds a new line after.
     *
     * @param string $output
     *
     * @return void
     */
    function nprint(string $output)
    {
        echo $output . '<br>';
    }
}
