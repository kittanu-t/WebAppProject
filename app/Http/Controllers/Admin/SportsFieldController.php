<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFieldRequest;
use App\Http\Requests\Admin\UpdateFieldRequest;
use App\Models\SportsField;
use App\Models\User;
use Illuminate\Http\Request;

class SportsFieldController extends Controller
{
    public function index(Request $request)
    {
        $q = SportsField::with('owner');

        if ($search = $request->query('q')) {
            $q->where(function($w) use ($search) {
                $w->where('name','like',"%$search%")
                  ->orWhere('sport_type','like',"%$search%")
                  ->orWhere('location','like',"%$search%");
            });
        }

        $fields = $q->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.fields.index', compact('fields'));
    }

    public function create()
    {
        $staffs = User::where('role','staff')->orderBy('name')->get(['id','name']);
        return view('admin.fields.create', compact('staffs'));
    }

    public function store(StoreFieldRequest $request)
    {
        $data = $request->validated();

        // owner ต้องเป็น staff
        if (!empty($data['owner_id'])) {
            $isStaff = User::where('id',$data['owner_id'])->where('role','staff')->exists();
            if (!$isStaff) {
                return back()->withErrors(['owner_id' => 'Owner ต้องเป็นผู้ใช้ role=staff เท่านั้น'])->withInput();
            }
        }

        SportsField::create($data);
        return redirect()->route('admin.fields.index')->with('status','สร้างสนามสำเร็จ');
    }

    public function show(SportsField $field)
    {
        return view('admin.fields.show', compact('field'));
    }

    public function edit(SportsField $field)
    {
        $staffs = User::where('role','staff')->orderBy('name')->get(['id','name']);
        return view('admin.fields.edit', compact('field','staffs'));
    }

    public function update(UpdateFieldRequest $request, SportsField $field)
    {
        $data = $request->validated();

        if (!empty($data['owner_id'])) {
            $isStaff = User::where('id',$data['owner_id'])->where('role','staff')->exists();
            if (!$isStaff) {
                return back()->withErrors(['owner_id' => 'Owner ต้องเป็นผู้ใช้ role=staff เท่านั้น'])->withInput();
            }
        }

        $field->update($data);
        return redirect()->route('admin.fields.index')->with('status','อัปเดตสนามสำเร็จ');
    }

    public function destroy(SportsField $field)
    {
        $field->delete();
        return redirect()->route('admin.fields.index')->with('status','ลบสนามสำเร็จ');
    }
}
