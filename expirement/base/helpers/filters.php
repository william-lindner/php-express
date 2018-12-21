<?php

/**
 * Sanitizes a data array request by removing HTML elements
 *
 */

function sanitize(array $set)
{
    return array_map(function ($point) {
        if (is_array($point)) {
            return sanitize($point);
        }

        if (is_string($point)) {
            $point = strip_tags(trim($point));
        }

        return $point;
    }, $set);
}
