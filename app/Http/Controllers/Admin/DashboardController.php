<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function index(){
        $title    = 'Dashboard';
        $data     = compact('title');
        return view('admin.dashboard',$data);
    }

    public function logOut()
    {
        Auth::logout();
        return redirect()->route('admin')->with('success','logout success.');
    }
    public function userList()
    {
        $title      = 'User list';
        $users      = User::where('role', 2)->get();
        $data       = compact('title', 'users');
        return view('admin.users.index',$data);
    }
}
