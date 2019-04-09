<?php

namespace App\Controllers;

use Express\Abstractions\Controller;

class HomeController extends Controller
{
    public function index()
    {
        view('index');
    }

}
