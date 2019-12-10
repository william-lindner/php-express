<?php

namespace App\Controllers;

use Express\Abstractions\Controller;
use Express\Traits\ViewHandler;
use Express\Request;

class HomeController extends Controller
{
    use ViewHandler;

    public function index(?Request $request)
    {
        return view('index');
    }
}
