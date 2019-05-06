<?php

namespace Express\Traits;

trait ViewHandler
{
    public function loadPageNotFound()
    {
        ob_clean();
        http_response_code(404);
        view('system/404');
        die;
    }
}
