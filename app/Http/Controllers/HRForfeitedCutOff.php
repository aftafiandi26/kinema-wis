<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HRForfeitedCutOff extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'hr']);
    }

    public function index($id)
    {
        dd($id);
    }
}
