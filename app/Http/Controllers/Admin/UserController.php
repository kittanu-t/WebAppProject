<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\SportsField;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        if ($search = $request->query('q')) {
            $q->where(function($w) use ($search) {
                $w->where('name','like',"%$search%")
                  ->orWhere('email','like',"%$search%");
            });
        }

        $users = $q->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:150','unique:users,email,'.$user->id],
            'phone' => ['nullable','string','max:30'],
            'role' => ['required','in:admin,staff,user'],   // ✅ ต้องมี staff อยู่ใน in:
            'active' => ['required','boolean'],
            'password' => ['nullable','confirmed','min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email'=> $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'active'=> $data['active'],
        ]);

        return redirect()->route('admin.users.index')->with('status','สร้างผู้ใช้สำเร็จ');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:150','unique:users,email,'.$user->id],
            'phone' => ['nullable','string','max:30'],
            'role' => ['required','in:admin,staff,user'],   // ✅ ต้องมี staff อยู่ใน in:
            'active' => ['required','boolean'],
            'password' => ['nullable','confirmed','min:6'],
        ]);

        // กันไม่ให้ de-activate / เปลี่ยน role ตัวเองจนไม่มี admin เหลือ
        if ($user->id === $request->user()->id) {
            if ($data['role'] !== 'admin') {
                throw ValidationException::withMessages(['role' => 'ไม่สามารถลดสิทธิ์แอดมินของตัวเองได้']);
            }
            if ($data['active'] == false) {
                throw ValidationException::withMessages(['active' => 'ไม่สามารถปิดการใช้งานบัญชีของตัวเองได้']);
            }
        }

        // อัปเดต
        $update = [
            'name' => $data['name'],
            'email'=> $data['email'],
            'role' => $data['role'],
            'phone'=> $data['phone'] ?? null,
            'active'=> $data['active'],
        ];
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('admin.users.index')->with('status','อัปเดตผู้ใช้สำเร็จ');
    }

    public function destroy(Request $request, User $user)
    {
        // กันลบตัวเอง / กันลบแอดมินคนสุดท้าย
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user'=>'ไม่สามารถลบบัญชีของตัวเองได้']);
        }
        if ($user->role === 'admin' && User::where('role','admin')->count() <= 1) {
            return back()->withErrors(['user'=>'ต้องมี admin อย่างน้อย 1 บัญชี']);
        }

        // ถ้าเป็น staff และเป็นเจ้าของสนาม ให้โอนหรือปล่อย owner_id = null
        if ($user->role === 'staff') {
            SportsField::where('owner_id',$user->id)->update(['owner_id'=>null]);
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('status','ลบผู้ใช้สำเร็จ');
    }
}
