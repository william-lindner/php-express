<?php

namespace App\Controllers;

use Express\Abstractions\Controller;
use Express\Traits\ViewHandler;

class HomeController extends Controller
{

    use ViewHandler;

    public function index()
    {
        // $this->loadPageNotFound();
        view('index');
    }

}
