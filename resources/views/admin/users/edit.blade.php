@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<h1 style="margin-bottom: 20px;">Edit User #{{ $user->id }}</h1>

@if($errors->any()) <div class="alert alert-danger mb-4">{{ $errors->first() }}</div> @endif

<form method="POST" action="{{ route('admin.users.update', $user) }}" class="card p-4 shadow-sm rounded-4" style="border: 1px solid #6C757D;">
  @csrf @method('PUT')
  <div class="mb-3"><label>Name</label> <input name="name" value="{{ old('name', $user->name) }}" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Email</label> <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Phone</label> <input name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3">
    <label>Role</label>
    <select name="role" required class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;">
      @foreach(['admin', 'staff', 'user'] as $r)
        <option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ ucfirst($r) }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label>Active</label>
    <select name="active" class="form-select" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;">
      <option value="1" @selected(old('active', $user->active) == '1')>Yes</option>
      <option value="0" @selected(old('active', $user->active) == '0')>No</option>
    </select>
  </div>
  <div class="mb-3"><label>Password (leave blank to keep)</label> <input name="password" type="password" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <div class="mb-3"><label>Confirm</label> <input name="password_confirmation" type="password" class="form-control" style="border: 1px solid #6C757D; border-radius: 8px; padding: 6px 12px;"></div>
  <button type="submit" class="btn fw-bold" style="background-color: #FFB900; color: #212529; border: none; border-radius: 8px; padding: 6px 12px;">Update</button>
</form>
@endsection