<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function index()
    {
        $title              = 'Dashboard';
        $totalUser          = User::where('role', 2)->count();
        $totalDriver        = User::where('role', 3)->count();
        $totalOrder         = Order::count();
        $todayOrder         = Order::whereDate('created_at', Carbon::today())->count();
        $data     = compact('title','totalUser','totalDriver','totalOrder','todayOrder');
        return view('admin.dashboard', $data);
    }

    public function logOut()
    {
        Auth::logout();
        return redirect()->route('admin')->with('success', 'logout success.');
    }
    public function userList()
    {
        $title      = 'User list';
        $users      = User::where('role', 2)->get();
        $data       = compact('title', 'users');
        return view('admin.users.index', $data);
    }
}
