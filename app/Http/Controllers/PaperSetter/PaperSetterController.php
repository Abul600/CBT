<?php

namespace App\Http\Controllers\PaperSetter;

use App\Http\Controllers\Controller;

class PaperSetterController extends Controller
{
    public function dashboard()
    {
        return view('paper_setter.dashboard');
    }
}
