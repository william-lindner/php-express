<?php

use Teapot\Route;

Route::get('/', function () {
    nprint('Found ya!');
});

Route::get('/about', function () {
    nprint('About page!');
});
