<?php

use Express\Route;

Route::get('/', function () {
    view('index');
});

Route::get('/about', function () {
    nprint('About page!');
});
