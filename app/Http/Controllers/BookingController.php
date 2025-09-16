<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // แสดงรายการ booking ของ user
        return view('user.bookings.index');
    }

    public function create()
    {
        // ฟอร์มสร้าง booking ใหม่
        return view('user.bookings.create');
    }

    public function store(Request $request)
    {
        // logic บันทึก booking
    }

    public function destroy($id)
    {
        // logic ยกเลิก booking
    }
}
