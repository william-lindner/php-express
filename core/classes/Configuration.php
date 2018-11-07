<?php

namespace Teapot;

class Configuration
{
    public static function load()
    {
        // TODO: Add in try catch
        return parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../.ini', true);
    }
}
