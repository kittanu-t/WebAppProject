<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportsField;
use App\Models\User;
use Illuminate\Http\Request;

class SportsFieldController extends Controller
{
    public function index(Request $request)
    {
        $q = SportsField::withCount('units')->with('owner');

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:120',
            'sport_type' => 'required|string|max:60',
            'location'   => 'required|string|max:200',
            'capacity'   => 'required|integer|min:0',
            'status'     => 'required|in:available,closed,maintenance',
            'owner_id'   => 'nullable|integer|exists:users,id',
            'min_duration_minutes' => 'required|integer|min:15',
            'max_duration_minutes' => 'required|integer|gte:min_duration_minutes',
            'lead_time_hours'      => 'required|integer|min:0',
        ]);

        // owner ต้องเป็น staff (ถ้ากำหนด)
        if (!empty($data['owner_id']) && !User::where('id',$data['owner_id'])->where('role','staff')->exists()) {
            return back()->withErrors(['owner_id' => 'Owner ต้องเป็น staff'])->withInput();
        }

        $field = SportsField::create($data);
        return redirect()->route('admin.fields.edit', $field)->with('status','สร้างสนามสำเร็จ — เพิ่ม Units ได้ที่แท็บด้านล่าง');
    }

    public function show(SportsField $field)
    {
        $field->loadCount('units')->load('owner');
        return view('admin.fields.show', compact('field'));
    }

    public function edit(SportsField $field)
    {
        $staffs = User::where('role','staff')->orderBy('name')->get(['id','name']);
        $field->loadCount('units')->load('owner');
        return view('admin.fields.edit', compact('field','staffs'));
    }

    public function update(Request $request, SportsField $field)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:120',
            'sport_type' => 'required|string|max:60',
            'location'   => 'required|string|max:200',
            'capacity'   => 'required|integer|min:0',
            'status'     => 'required|in:available,closed,maintenance',
            'owner_id'   => 'nullable|integer|exists:users,id',
            'min_duration_minutes' => 'required|integer|min:15',
            'max_duration_minutes' => 'required|integer|gte:min_duration_minutes',
            'lead_time_hours'      => 'required|integer|min:0',
        ]);

        if (!empty($data['owner_id']) && !User::where('id',$data['owner_id'])->where('role','staff')->exists()) {
            return back()->withErrors(['owner_id' => 'Owner ต้องเป็น staff'])->withInput();
        }

        $field->update($data);
        return redirect()->route('admin.fields.edit',$field)->with('status','อัปเดตสนามสำเร็จ');
    }

    public function destroy(SportsField $field)
    {
        $field->delete();
        return redirect()->route('admin.fields.index')->with('status','ลบสนามสำเร็จ');
    }
}
