<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index');
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        // logic บันทึก announcement
    }

    public function show($id)
    {
        return view('admin.announcements.show');
    }

    public function edit($id)
    {
        return view('admin.announcements.edit');
    }

    public function update(Request $request, $id)
    {
        // logic อัพเดท announcement
    }

    public function destroy($id)
    {
        // logic ลบ announcement
    }
}
