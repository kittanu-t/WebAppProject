<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SportsFieldController extends Controller
{
    public function index()
    {
        return view('admin.fields.index');
    }

    public function create()
    {
        return view('admin.fields.create');
    }

    public function store(Request $request)
    {
        // logic สร้าง field
    }

    public function show($id)
    {
        return view('admin.fields.show');
    }

    public function edit($id)
    {
        return view('admin.fields.edit');
    }

    public function update(Request $request, $id)
    {
        // logic อัพเดท field
    }

    public function destroy($id)
    {
        // logic ลบ field
    }
}
