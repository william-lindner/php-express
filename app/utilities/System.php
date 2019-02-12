<?php

namespace App\Utilities;

class System
{
    public static function pageNotFound()
    {
        ob_clean();
        http_response_code(404);
        view('system/404');
        die;
    }

    /**
     * Tests an insert id and responds with 404
     */
    public static function checkInsert($id)
    {
        if (!$testInsert) {
            ob_clean();
            http_response_code(400);
            header('HTTP/1.1 400 Bad Request');
            die;
        }
    }
}
