<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeadOfDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'hd']);
    }

    public function indexAccess()
    {
        return view('admin.HDLevelAccess');
    }
}
