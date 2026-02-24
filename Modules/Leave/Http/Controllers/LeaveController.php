<?php

namespace Modules\Leave\Http\Controllers;

use Illuminate\Routing\Controller;

class LeaveController extends Controller
{
    public function index()
    {
        return view('leave::index');
    }
}
