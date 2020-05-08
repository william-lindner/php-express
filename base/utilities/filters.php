<?php

if (!function_exists('sanitize')) {
    /**
     * Sanitizes a data array request by removing HTML elements
     *
     * @param $data
     *
     * @return array
     */
    function sanitize(array $data)
    {
        return Express\Http\Request::sanitize($data);
    }
}