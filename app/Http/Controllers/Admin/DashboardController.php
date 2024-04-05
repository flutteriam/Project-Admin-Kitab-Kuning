<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}
