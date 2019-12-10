<?php

use Express\Route;
use Express\Request;

Route::get('/', 'HomeController@index');

Route::get('/closure', function (Request $request, $rando = null) {
    dd('closure ran!');
});
