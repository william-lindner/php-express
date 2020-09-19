<?php

namespace App\Controllers;

use Express\Diagnostic;
use Express\Abstractions\Controller;
use Express\Http\Request;

class ApiController extends Controller
{
    public function show(Request $request)
    {
        return $this->response()->json(
            [
                'peak_mem' => Diagnostic::memory()->peak(),
                'curr_mem' => Diagnostic::memory()->current()
            ]
        );
    }
}