<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // แสดง booking ที่ pending รออนุมัติ (เฉพาะ field ที่ staff เป็น owner)
        return view('staff.bookings.index');
    }

    public function approve($id)
    {
        // logic อนุมัติ booking
    }

    public function reject($id)
    {
        // logic ปฏิเสธ booking
    }
}
