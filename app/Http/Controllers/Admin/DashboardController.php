<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // แสดงข้อมูลสถิติระบบ เช่น จำนวน user, booking, field
        return view('admin.dashboard');
    }
}
