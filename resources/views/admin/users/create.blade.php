@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<h1 style="margin-bottom: 20px;">Create User</h1>

<form method="POST" action="{{ route('admin.users.store') }}" class="card p-4 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
  @csrf
  <div class="mb-3"><label>Name</label> <input name="name" value="{{ old('name') }}" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Email</label> <input name="email" type="email" value="{{ old('email') }}" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Phone</label> <input name="phone" value="{{ old('phone') }}" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3">
    <label>Role</label>
    <select name="role" required class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;">
      @foreach(['admin', 'staff', 'user'] as $r)
        <option value="{{ $r }}" @selected(old('role') === $r)>{{ ucfirst($r) }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label>Active</label>
    <select name="active" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;">
      <option value="1" @selected(old('active', '1') == '1')>Yes</option>
      <option value="0" @selected(old('active') == '0')>No</option>
    </select>
  </div>
  <div class="mb-3"><label>Password</label> <input name="password" type="password" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Confirm</label> <input name="password_confirmation" type="password" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px;">Save</button>
</form>
@endsection