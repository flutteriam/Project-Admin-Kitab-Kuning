<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBooks = Book::count();
        return view('admin.dashboard', compact('totalUsers', 'totalBooks'));
    }
}
