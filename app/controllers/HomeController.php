<?php

namespace App\Controllers;

use Express\Diagnostic;
use Express\Abstractions\Controller;
use Express\Traits\ViewHandler;
use Express\Http\Request;

class HomeController extends Controller
{
    use ViewHandler;

    public function index(?Request $request)
    {
        //        dump($request);
        //
        //        dump(Diagnostic::memory()->peak());
        //        dump(Diagnostic::memory()->current());

        return view('index');
    }
}
