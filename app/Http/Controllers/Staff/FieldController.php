<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;

class FieldController extends Controller
{
    public function schedule()
    {
        // แสดง calendar schedule ของ field ที่ staff ดูแล
        return view('staff.fields.schedule');
    }
}
