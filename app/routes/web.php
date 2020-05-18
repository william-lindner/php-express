<?php

use Express\Router;
use Express\Http\Request;

Router::get('/', 'HomeController@index');

Router::get('/api', 'ApiController@show');

Router::get(
    '/closure',
    static function (Request $request, $rando = null) {
        echo Express\Diagnostic::memory()->peak(false);
        dump($request);
        dd('closure ran!');
    }
);
